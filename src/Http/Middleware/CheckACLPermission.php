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

namespace Seatplus\Web\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\User;
use Symfony\Component\HttpFoundation\Response;

class CheckACLPermission
{
    private const PERMISSION_DENIED_MESSAGE = 'You do not have the necessary permission to perform this action.';

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->hasAdministrativeAccess()) {
            return $next($request);
        }

        $requestedRoleId = $request->route()->parameter('role_id');
        $moderatedRoleIds = $this->getModeratedRoleIds();

        if (! $this->hasRequiredPermissions($requestedRoleId, $moderatedRoleIds)) {
            abort(403, self::PERMISSION_DENIED_MESSAGE);
        }

        return $next($request);
    }

    private function hasAdministrativeAccess(): bool
    {
        return auth()->user()->can('superuser') ||
               auth()->user()->can('view access control');
    }

    private function getModeratedRoleIds(): Collection
    {
        $userId = auth()->user()->getAuthIdentifier();

        return RoleMembership::query()
            ->where('can_moderate', true)
            ->whereHasMorph('entity', [User::class], fn (Builder $query) => $query->whereId($userId))
            ->pluck('role_id');
    }

    private function hasRequiredPermissions(?int $requestedRoleId, Collection $moderatedRoleIds): bool
    {
        if (! $requestedRoleId) {
            return $moderatedRoleIds->isNotEmpty();
        }

        return in_array($requestedRoleId, $moderatedRoleIds->toArray());
    }
}
