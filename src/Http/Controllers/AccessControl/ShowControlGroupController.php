<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Models\Permissions\Affiliation;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Web\Http\Controllers\Controller;

class ShowControlGroupController extends Controller
{
    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(int $role_id): Response|RedirectResponse
    {
        $role = Role::with('affiliations.affiliatable', 'role_memberships.entity', 'permissions')
            ->findOrFail($role_id);

        $user = auth()->user();

        abort_unless(
            $user->can('superuser')
                || $user->can('administrate access control groups')
                || $this->baseRoleService->for($role)->canModerate($user),
            403
        );

        return Inertia::render('AccessControl/RoleDetail', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'type' => $role->type->value,
                'affiliations' => $role->affiliations->map(fn (Affiliation $affiliation) => [
                    'id' => $affiliation->affiliatable_id,
                    'category' => $affiliation->affiliatable_type,
                    'type' => $affiliation->type,
                ]),
                'permissions' => $role->permissions->pluck('name'),
            ],
            'activeSidebarElement' => 'acl.groups',
        ]);
    }
}
