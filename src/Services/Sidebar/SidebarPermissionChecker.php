<?php

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

namespace Seatplus\Web\Services\Sidebar;

use Illuminate\Database\Eloquent\Builder;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Permissions\CanUserService;
use Seatplus\Auth\Services\Permissions\DTO\ValidateIdsDTO;

class SidebarPermissionChecker
{
    public function __construct(
        private readonly User $user,
        private ?CanUserService $canUserService = null,
    ) {
        $this->canUserService = $canUserService ?? new CanUserService;
    }

    public function hasPermissionOrCorporationRole(string|array $permissions, string|array $characterRoles = ''): bool
    {
        return $this->canUserService->check(
            user: $this->user,
            idsDTO: (new ValidateIdsDTO),
            permissions: $this->normalizeInput($permissions),
            corporation_roles: $this->normalizeInput($characterRoles),
        );
    }

    public function isRoleModerator(): bool
    {
        return RoleMembership::query()
            ->where('can_moderate', true)
            ->whereHasMorph('entity', [User::class], fn (Builder $query) => $query->whereId($this->user->getAuthIdentifier()))
            ->exists();
    }

    /**
     * @param  string|array<int,string>  $input
     * @return array<int,string>
     */
    private function normalizeInput(string|array $input): array
    {
        $array = is_array($input) ? $input : [$input];

        return collect($array)
            ->flatMap(fn (string $item) => explode(',', $item))
            ->flatMap(fn (string $item) => explode('|', $item))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }
}
