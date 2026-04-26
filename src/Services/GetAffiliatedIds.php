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

namespace Seatplus\Web\Services;

use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Permissions\CanUserService;

class GetAffiliatedIds
{
    private const DIRECTOR_ROLE = 'Director';

    public function __construct(
        private ?User $user = null,
        private ?CanUserService $canUserService = null
    ) {
        $this->user = $user ?? auth()->user();
        $this->canUserService = $canUserService ?? new CanUserService;
    }

    /**
     * @param  string|array<int,string>  $permissions
     * @param  string|array<int,string>  $corporationRoles
     * @return array<int,int>
     */
    public function get(
        string|array $permissions,
        string|array $corporationRoles = [],
        ?User $user = null
    ): array {
        $normalized_permissions = $this->normalizeInput($permissions);
        $normalizedRoles = $this->normalizeInput($corporationRoles);
        $normalizedRoles[] = self::DIRECTOR_ROLE;

        $this->user = $user ?? $this->user;

        return (new self($user))->collectAffiliatedIds($normalized_permissions, $normalizedRoles);
    }

    /**
     * @param  array<int,string>  $permissions
     * @param  array<int,string>  $corporationRole
     * @return array<int,int>
     */
    private function collectAffiliatedIds(array $permissions, array $corporationRole): array
    {
        $userPermission = $this->canUserService->getUserPermissionObject($this->user);

        return array_merge(
            $this->getPermissionBasedIds($permissions, $userPermission),
            $this->getCorporationRoleBasedIds($corporationRole, $userPermission),
            data_get($userPermission, 'owned_character_ids', [])
        );
    }

    /**
     * @param  string|array<int,string>  $input
     * @return array<int,string>
     */
    private function normalizeInput(string|array $input): array
    {
        $array = is_array($input) ? $input : [$input];

        return collect($array)
            ->flatMap(fn (string|array $item) => is_string($item) ? explode(',', $item) : [$item])
            ->flatMap(fn (string|array $item) => is_string($item) ? explode('|', $item) : [$item])
            ->map(fn (string|array $item) => is_string($item) ? trim($item) : $item)
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * @param  array<int,string>  $permissions
     * @return array<int,int>
     */
    private function getPermissionBasedIds(array $permissions, array $userPermission): array
    {
        return collect($permissions)
            ->map(fn (string $permission) => data_get($userPermission, "permissions.$permission", []))
            ->collapse()
            ->toArray();
    }

    /**
     * @param  array<int,string>  $corporation_role
     * @return array<int,int>
     */
    private function getCorporationRoleBasedIds(array $corporation_role, array $userPermission): array
    {
        return collect($corporation_role)
            ->map(fn (string $corporation_role) => data_get($userPermission, "corporation_roles.$corporation_role", []))
            ->collapse()
            ->toArray();
    }
}
