<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\RoleDetailResource;
use Seatplus\Web\Support\AccessControl\RoleTypeMetadata;
use Seatplus\Web\Support\Translations;

class ManageControlGroupMembersController extends Controller
{
    public function index(int $role_id): Response
    {
        $role = Role::with('affiliations.affiliatable', 'roleMemberships.entity', 'permissions')
            ->findOrFail($role_id);

        return Inertia::render('AccessControl/ManageControlGroup', [
            'role' => (new RoleDetailResource($role))->resolve(),
            // Every join method + its capability rules, so the picker can explain each option
            // and the form knows whether to show the eligibility section / which endpoint to POST.
            'joinMethods' => RoleTypeMetadata::all(),
            'activeSidebarElement' => 'acl.groups',
            'pageTranslations' => Translations::gather(['web::access_control']),
        ]);
    }
}
