<?php


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
            'roles' => $roles
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
            'permissions' => $role->permissions
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
