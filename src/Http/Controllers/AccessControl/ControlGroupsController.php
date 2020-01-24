<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
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
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Web\Http\Controllers\Request\UpdateControlGroup;
use Seatplus\Web\Http\Resources\RoleRessource;
use Seatplus\Web\Services\ACL\SyncRoleAffiliations;
use Seatplus\Web\Services\ACL\SyncRoleName;
use Seatplus\Web\Services\ACL\SyncRolePermissions;

class ControlGroupsController
{
    public function index(Request $request)
    {
        $query = Role::query();

        $roles = RoleRessource::collection(
            $query->paginate()
        );

        return Inertia::render('AccessControl/ControlGroups', [
            'roles' => $roles,
        ]);
    }

    public function create(Request $request)
    {
        $name = $request->input('input');

        $role = Role::create(['name' => $name]);

        return redirect()
            ->action([ControlGroupsController::class, 'edit'], $role->role_id)
            ->with('success', 'Role was created');
    }

    public function edit(int $role_id)
    {

        $role = Role::findById($role_id);

        $permissions = function () {
            return array_merge(Arr::flatten(config('eveapi.permissions')), config('web.permissions'));
        };

        return Inertia::render('AccessControl/EditGroup', [
            'role' => $role,
            'affiliations' => $role->affiliations,
            'available_permissions' => $permissions,
            'permissions' => $role->permissions,
        ]);
    }

    public function update(UpdateControlGroup $request, int $role_id)
    {

        $validated_data = $request->all();

        $role = Role::findById($role_id);

        (new SyncRolePermissions($role))->sync($validated_data);

        (new SyncRoleAffiliations($role))->sync($validated_data);

        (new SyncRoleName($role))->sync($validated_data['roleName']);

        return redirect()
            ->action([ControlGroupsController::class, 'edit'], $role_id)
            ->with('success', 'Access control group updated');
    }
}
