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

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Request\UpdateControlGroup;
use Seatplus\Web\Services\ACL\SyncRoleAffiliations;
use Seatplus\Web\Services\ACL\SyncRoleName;
use Seatplus\Web\Services\ACL\SyncRolePermissions;
use Seatplus\Web\Services\SearchService;

class ControlGroupsController
{
    public function index()
    {
        return Inertia::render('AccessControl/ControlGroupsIndex');
    }

    public function create(Request $request)
    {
        $name = $request->input('name');

        $role = Role::create(['name' => $name]);

        return redirect()
            ->action([ControlGroupsController::class, 'edit'], $role->id)
            ->with('success', 'Role was created');
    }

    public function edit(int $role_id)
    {
        $role = Role::findById($role_id);

        $permissions = function () {
            return array_merge(Arr::flatten(config('eveapi.permissions')), config('web.permissions'));
        };

        $existing_affiliations = fn () => $role->affiliations->map(fn ($affiliation) => [
            'id' => $affiliation->affiliatable_id,
            'category' => $affiliation->affiliatable_type,
            'type' => $affiliation->type,
        ]);

        return Inertia::render('AccessControl/EditGroup', [
            'role' => $role,
            'affiliations' => $existing_affiliations,
            'available-permissions' => $permissions,
            'permissions' => $role->permissions,
            'activeSidebarElement' => route('acl.groups'),
        ]);
    }

    public function update(UpdateControlGroup $request, int $role_id)
    {
        $validated_data = $request->all();

        $role = Role::findById($role_id);

        (new SyncRolePermissions($role))->sync($validated_data);

        (new SyncRoleAffiliations($role))->sync($validated_data);

        if (Arr::has($validated_data, 'roleName')) {
            (new SyncRoleName($role))->sync($validated_data['roleName']);
        }

        return redirect()
            ->action([ControlGroupsController::class, 'edit'], $role_id)
            ->with('success', 'Access control group updated');
    }

    public function search()
    {
        $query = request()->get('query');

        $result = $query ? (new SearchService)->execute(['character', 'corporation', 'alliance'], $query) : $this->getFirstSelection();

        return $this->paginate(
            collect($result)
                ->flatMap(fn ($result, $category) => collect($result)
                    ->map(fn ($res) => [
                        'id' => $res,
                        'category' => $category,
                    ]))
        );
    }

    private function paginate(array|Collection $items, int $perPage = 15, ?int $page = null, array $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    private function getFirstSelection(): array
    {
        $alliance_ids = AllianceInfo::query()->take(15)->inRandomOrder()->pluck('alliance_id');
        $corporation_ids = CorporationInfo::query()->take(15)->inRandomOrder()->pluck('corporation_id');
        $character_ids = CharacterInfo::query()->take(15)->inRandomOrder()->pluck('character_id');

        $ids = collect([...$alliance_ids, ...$corporation_ids, ...$character_ids])
            ->shuffle()
            ->take(15);

        return [
            'alliance' => $alliance_ids->intersect($ids)->toArray(),
            'corporation' => $corporation_ids->intersect($ids)->toArray(),
            'character' => $character_ids->intersect($ids)->toArray(),
        ];
    }
}
