<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Web\Http\Controllers\Request\UpdateControlGroup;
use Seatplus\Web\Services\ACL\SyncRoleAffiliations;
use Seatplus\Web\Services\ACL\SyncRoleName;
use Seatplus\Web\Services\ACL\SyncRolePermissions;

class UpdateControlGroupController
{
    public function __invoke(UpdateControlGroup $request, int $role_id): RedirectResponse
    {
        $validated_data = $request->all();

        $role = Role::findById($role_id);

        (new SyncRolePermissions($role))->sync($validated_data);

        (new SyncRoleAffiliations($role))->sync($validated_data);

        if (Arr::has($validated_data, 'roleName')) {
            (new SyncRoleName($role))->sync($validated_data['roleName']);
        }

        return redirect()
            ->route('acl.edit', $role_id)
            ->with('success', 'Access control group updated');
    }
}
