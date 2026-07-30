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
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Services\HasCharacterNecessaryRole;

class SidebarPermissionChecker
{
    public function __construct(
        private readonly User $user,
    ) {
        // eager-load once so the corporation-role check below never lazy-loads per character
        $this->user->loadMissing('characters.roles');
    }

    /**
     * Whether a sidebar entry should be shown: a superuser sees everything (the `Gate::before` rule),
     * the user holds one of the permissions (Spatie), or one of their characters holds one of the required
     * in-game corporation roles (Director always matches).
     *
     * @param  string|array<int,string>  $permissions
     * @param  string|array<int,string>  $characterRoles
     */
    public function hasPermissionOrCorporationRole(string|array $permissions, string|array $characterRoles = ''): bool
    {
        // superuser short-circuits via the Gate::before rule; safe even if the permission is unregistered
        if ($this->user->can('superuser')) {
            return true;
        }

        $permissions = $this->normalizeInput($permissions);

        if ($permissions !== [] && $this->user->hasAnyPermission($permissions)) {
            return true;
        }

        $characterRoles = $this->normalizeInput($characterRoles);

        if ($characterRoles === []) {
            return false;
        }

        return $this->user->characters
            ->contains(fn (CharacterInfo $character) => HasCharacterNecessaryRole::check($character, $characterRoles));
    }

    public function isRoleModerator(): bool
    {
        return RoleMembership::query()
            ->where('can_moderate', true)
            ->whereHasMorph('entity', [User::class], fn (Builder $query) => $query->where('id', $this->user->getAuthIdentifier()))
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
