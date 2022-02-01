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
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Services\GetRecruitIdsService;
use Seatplus\Web\Services\HasCharacterNecessaryRole;
use Symfony\Component\HttpFoundation\ParameterBag;

class CheckPermissionAffiliation
{
    private Collection $affiliated_ids;
    private User $user;

    public function __construct()
    {
        $this->affiliated_ids = collect();
    }

    /**
     * @param Request     $request
     * @param Closure     $next
     * @param string      $permission
     * @param string|null $character_role
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission, ?string $character_role = null)
    {
        $validated_data = $request->validate([
            'character_ids'   => ['sometimes', 'array'],
            'corporation_ids' => ['sometimes', 'array'],
            'alliance_ids'    => ['sometimes', 'array'],
        ]);

        $this->setUser();

        if ($this->getUser()->can('superuser')) {
            $this->appendValidatedIds($request->request, collect($validated_data)->flatten());

            return $next($request);
        }

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        $url_parameters = $request->route()->parameters();

        $requested_id = collect([
            Arr::get($url_parameters, 'character_id'),
            Arr::get($url_parameters, 'corporation_id'),
            Arr::get($url_parameters, 'alliance_id'),
        ])->filter();

        $requested_ids = collect($validated_data)->merge($requested_id)->flatten();

        if ($requested_ids->isEmpty()) {
            abort_unless($this->assertIfUserHasRequiredPermissionOrCharacterRole($permissions, $character_role), 403, 'You do not have the necessary permission to perform this action.');

            return $next($request);
        }

        $this->buildAffiliatedIdsByPermissions($permissions);

        $this->buildRecruitIds();

        if (is_string($character_role)) {
            $this->buildAffiliatedIdsByCharacterRole($character_role);
        }

        $validated_ids = $requested_ids->intersect($this->getAffiliatedIds()->toArray());

        abort_unless($validated_ids->isNotEmpty(), 403, 'You are not allowed to access the requested entity');

        $this->appendValidatedIds($request->request, $validated_ids);

        return $next($request);
    }

    /**
     * @return Collection
     */
    public function getAffiliatedIds(): Collection
    {
        return $this->affiliated_ids
            ->flatten()
            ->unique();
    }

    private function checkUserPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->getUser()->can($permission)) {
                return true;
            }
        }

        return false;
    }

    private function buildAffiliatedIdsByPermissions(array $permissions): void
    {
        foreach ($permissions as $permission) {
            $affiliatedIds = getAffiliatedIdsByPermission($permission);

            $this->affiliated_ids->push($affiliatedIds);
        }
    }

    private function buildRecruitIds(): void
    {
        $recruit_ids = GetRecruitIdsService::get();

        $this->affiliated_ids->push($recruit_ids);
    }

    private function assertIfUserHasRequiredPermissionOrCharacterRole(array $permissions, ?string $character_role)
    {
        if ($this->checkUserPermissions($permissions)) {
            return true;
        }

        if (is_null($character_role)) {
            return false;
        }

        return $this->checkUserCharacterRoles($character_role);
    }

    private function checkUserCharacterRoles(?string $character_role): bool
    {
        if (is_null($character_role)) {
            return false;
        }

        return empty($this->buildAffiliatedIdsByCharacterRole($character_role)) ? false : true;
    }

    private function buildAffiliatedIdsByCharacterRole(string $character_role): array
    {
        $affiliated_ids_from_character_role = $this->getUser()
            ->load(['characters.roles', 'characters.corporation'])
            ->characters
            ->map(
                fn ($character) => HasCharacterNecessaryRole::check($character, $character_role)
                ? $character->corporation->corporation_id
                : false
            )
            ->filter()
            ->unique()
            ->toArray();

        $this->affiliated_ids->push($affiliated_ids_from_character_role);

        return $affiliated_ids_from_character_role;
    }

    private function appendValidatedIds(ParameterBag $bag, Collection $validated_ids)
    {
        $bag->add(['validated_ids' => $validated_ids->all()]);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(): void
    {
        $this->user = User::find(auth()->user()->getAuthIdentifier());
    }
}
