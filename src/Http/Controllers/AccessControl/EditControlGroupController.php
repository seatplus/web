<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Models\Permissions\Affiliation;
use Seatplus\Auth\Models\Permissions\Role;

class EditControlGroupController
{
    public function __invoke(int $role_id): Response
    {
        /** @var Role $role */
        $role = Role::findById($role_id);

        $permissions = fn () => array_merge(Arr::flatten(config('eveapi.permissions')), config('web.permissions'), config('auth.permissions', []));

        $existing_affiliations = fn () => $role->affiliations->map(fn (Affiliation $affiliation) => [
            'id' => $affiliation->affiliatable_id,
            'category' => $affiliation->affiliatable_type,
            'type' => $affiliation->type,
        ]);

        return Inertia::render('AccessControl/EditGroup', [
            'role' => $role,
            'affiliations' => $existing_affiliations,
            'available-permissions' => $permissions,
            'permissions' => $role->permissions,
            'activeSidebarElement' => 'acl.groups',
        ]);
    }
}
