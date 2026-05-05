<?php

declare(strict_types=1);

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Seatplus\Web\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Permissions\CanUserService;
use Seatplus\Auth\Services\Permissions\DTO\ValidateIdsDTO;
use Seatplus\Web\Services\Affiliations\GetCorporationMemberComplianceAffiliatedIdsService;
use Seatplus\Web\Services\GetRecruitIdsService;

class CheckAuthorizationWithExtendedScope
{
    public function __construct(
        private readonly CanUserService $canUserService = new CanUserService,
    ) {}

    public function handle(Request $request, Closure $next, string $permissions, ?string $corporation_role = null): mixed
    {
        /** @var User $user */
        $user = auth()->user();

        $idsDTO = ValidateIdsDTO::fromRequest($request);
        $permissionsArray = explode('|', $permissions);
        $corporationRoles = explode('|', $corporation_role ?? '');

        // Primary check — covers own characters, Spatie permissions, and corporation roles.
        if ($this->canUserService->check($user, $idsDTO, $permissionsArray, $corporationRoles)) {
            return $next($request);
        }

        // Extended scope only applies when a specific charas present in the route.
        $routeCharacterId = $request->route('character_id');
        if (! is_numeric($routeCharacterId)) {
            abort(403);
        }

        $character_id = (int) $routeCharacterId;

        // Compliance reviewer fallback: users with the member-compliance permission can access
        // any character belonging to a user in their affiliated compliance scope.
        if ($user->can('member compliance: review user')) {
            $complianceCharacterIds = GetCorporationMemberComplianceAffiliatedIdsService::make()
                ->getQuery()
                ->pluck('affiliated_id');

            if ($complianceCharacterIds->contains($character_id)) {
                return $next($request);
            }
        }

        // Recruiter fallback: users with the recruitment permission can access
        // any character that has an open application to their managed corporations.
        if ($user->can('can accept or deny applications')) {
            $recruitCharacterIds = GetRecruitIdsService::get();

            if (in_array($character_id, $recruitCharacterIds, true)) {
                return $next($request);
            }
        }

        abort(403);
    }
}
