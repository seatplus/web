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

use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Permissions\CanUserService;
use Seatplus\Auth\Services\Roles\AffiliationResolver;

class GetAffiliatedIds
{
    private const string DIRECTOR_ROLE = 'Director';

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
     * The user's own character ids, taken straight from the cached permission object
     * (user_permissions_{id}, 5-min TTL) rather than a fresh query — the same slice
     * collectAffiliatedIds() merges in as `owned_character_ids`.
     *
     * @return array<int, int>
     */
    public function ownedCharacterIds(?User $user = null): array
    {
        // Re-resolve auth() at call time: a container-injected instance may have been
        // built before the auth middleware ran, leaving $this->user null.
        $user = $user ?? $this->user ?? auth()->user();

        return data_get($this->canUserService->getUserPermissionObject($user), 'owned_character_ids', []);
    }

    /**
     * Constrain $column to the entities affiliated via $permissions / $corporationRoles, composed as a
     * grouped subquery rather than a materialised id array: the affiliated set is resolved in SQL by
     * {@see AffiliationResolver}, so an inverse or alliance-wide role never pulls a whole *_infos table
     * into PHP the way get() does.
     *
     * The whole affiliation constraint is wrapped in a single nested where(), so it stays safe to chain
     * next to an existing orWhere (no boolean-precedence surprise). $column selects the id-space — the
     * two spaces get() ever filtered against:
     *  - a character_id column matches the affiliated characters OR the user's own characters;
     *  - a corporation_id column matches the affiliated corporations OR the corporations the user holds
     *    $corporationRoles (+ Director) in.
     * Any other column throws (fail closed).
     *
     * A superuser is authorised for everything, so no constraint is added (mirrors the `superuser`
     * bypass CanUserService/`Gate::before` apply everywhere else).
     *
     * @param  string|array<int,string>  $permissions
     * @param  string|array<int,string>  $corporationRoles
     */
    public function scope(
        Builder $query,
        string $column,
        string|array $permissions,
        string|array $corporationRoles = [],
        ?User $user = null,
    ): Builder {
        $user = $user ?? $this->user ?? auth()->user();

        if ($user->can('superuser')) {
            return $query;
        }

        $userPermission = $this->canUserService->getUserPermissionObject($user);

        $roleIds = $this->permissionRoleIds($this->normalizeInput($permissions), $userPermission);
        $resolver = new AffiliationResolver;

        if (str_contains($column, 'character_id')) {
            $ownedCharacterIds = data_get($userPermission, 'owned_character_ids', []);

            return $query->where(fn (Builder $builder) => $builder
                ->whereIn($column, $resolver->characterIdsSubquery($roleIds))
                ->orWhereIn($column, $ownedCharacterIds));
        }

        if (str_contains($column, 'corporation_id')) {
            $normalizedRoles = $this->normalizeInput($corporationRoles);
            $normalizedRoles[] = self::DIRECTOR_ROLE;
            $corporationRoleIds = $this->corporationRoleIds($normalizedRoles, $userPermission);

            return $query->where(fn (Builder $builder) => $builder
                ->whereIn($column, $resolver->corporationIdsSubquery($roleIds))
                ->orWhereIn($column, $corporationRoleIds));
        }

        throw new InvalidArgumentException("GetAffiliatedIds::scope() cannot resolve an id-space for column [{$column}].");
    }

    /**
     * Constrain $column to the entities the user *owns / operates* — the default view before any
     * explicit selection:
     *  - a character_id column matches only the user's own characters;
     *  - a corporation_id column matches only the corporations the user is a member of AND holds
     *    $corporationRoles (or Director) in — i.e. the cached `corporation_roles` slice.
     * Unlike {@see scope()} this deliberately excludes the merely-affiliated set (entities reachable
     * through a permission, e.g. an alliance-wide auditor role); those appear only when the user
     * explicitly selects them, and a selection is validated through scope().
     *
     * @param  string|array<int,string>  $corporationRoles
     */
    public function scopeOwned(
        Builder $query,
        string $column,
        string|array $corporationRoles = [],
        ?User $user = null,
    ): Builder {
        $user = $user ?? $this->user ?? auth()->user();
        $userPermission = $this->canUserService->getUserPermissionObject($user);

        if (str_contains($column, 'character_id')) {
            return $query->whereIn($column, data_get($userPermission, 'owned_character_ids', []));
        }

        if (str_contains($column, 'corporation_id')) {
            $normalizedRoles = $this->normalizeInput($corporationRoles);
            $normalizedRoles[] = self::DIRECTOR_ROLE;

            return $query->whereIn($column, $this->corporationRoleIds($normalizedRoles, $userPermission));
        }

        throw new InvalidArgumentException("GetAffiliatedIds::scopeOwned() cannot resolve an id-space for column [{$column}].");
    }

    /**
     * Whether $corporationId is one of the corporations affiliated via $permissions / $corporationRoles —
     * a bounded membership test that never materialises the affiliated set. Replaces
     * `in_array($corporationId, $this->get(...))` on the corporation id-space.
     *
     * @param  string|array<int,string>  $permissions
     * @param  string|array<int,string>  $corporationRoles
     */
    public function coversCorporation(
        int $corporationId,
        string|array $permissions,
        string|array $corporationRoles = [],
        ?User $user = null,
    ): bool {
        $user = $user ?? $this->user ?? auth()->user();
        $userPermission = $this->canUserService->getUserPermissionObject($user);

        $normalizedRoles = $this->normalizeInput($corporationRoles);
        $normalizedRoles[] = self::DIRECTOR_ROLE;

        if (in_array($corporationId, $this->corporationRoleIds($normalizedRoles, $userPermission), true)) {
            return true;
        }

        $roleIds = $this->permissionRoleIds($this->normalizeInput($permissions), $userPermission);

        return (new AffiliationResolver)->coveredIds($roleIds, [$corporationId]) !== [];
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
            ->flatMap(fn (string $item) => explode(',', $item))
            ->flatMap(fn (string $item) => explode('|', $item))
            ->map(fn (string $item) => trim($item))
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Resolve the affiliated ids granted by the given permissions live, via {@see AffiliationResolver}.
     *
     * The cached permission object no longer carries a materialised `permissions` id-array slice (removed in
     * auth 5.0); it carries `permission_roles` (permission → the ids of the user's roles that grant it). We
     * union those role ids and enumerate their affiliated character/corporation/alliance ids through the
     * resolver's set-based subqueries — the conflated, cross-id-space array shape the consumers expect.
     *
     * @param  array<int,string>  $permissions
     * @return array<int,int>
     */
    private function getPermissionBasedIds(array $permissions, array $userPermission): array
    {
        $roleIds = $this->permissionRoleIds($permissions, $userPermission);

        if ($roleIds === []) {
            return [];
        }

        $resolver = new AffiliationResolver;

        $affiliatedIds = array_merge(
            $resolver->characterIdsSubquery($roleIds)->pluck('affiliated_id')->all(),
            $resolver->corporationIdsSubquery($roleIds)->pluck('affiliated_id')->all(),
            $resolver->allianceIdsSubquery($roleIds)->pluck('affiliated_id')->all(),
        );

        return array_map('intval', $affiliatedIds);
    }

    /**
     * @param  array<int,string>  $corporation_role
     * @return array<int,int>
     */
    private function getCorporationRoleBasedIds(array $corporation_role, array $userPermission): array
    {
        return $this->corporationRoleIds($corporation_role, $userPermission);
    }

    /**
     * The ids of the user's roles that grant any of $permissions, from the cached `permission_roles` slice.
     *
     * @param  array<int,string>  $permissions
     * @param  array<string,mixed>  $userPermission
     * @return array<int,int>
     */
    private function permissionRoleIds(array $permissions, array $userPermission): array
    {
        return collect($permissions)
            ->flatMap(fn (string $permission) => data_get($userPermission, "permission_roles.$permission", []))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * The corporation ids the user holds any of $corporationRoles in, from the cached `corporation_roles` slice.
     *
     * @param  array<int,string>  $corporationRoles
     * @param  array<string,mixed>  $userPermission
     * @return array<int,int>
     */
    private function corporationRoleIds(array $corporationRoles, array $userPermission): array
    {
        return collect($corporationRoles)
            ->flatMap(fn (string $role) => data_get($userPermission, "corporation_roles.$role", []))
            ->map(fn (mixed $id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }
}
