<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\RoleDetailResource;
use Seatplus\Web\Support\Translations;

class ShowControlGroupController extends Controller
{
    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(int $role_id): Response|RedirectResponse
    {
        $role = Role::with('affiliations.affiliatable', 'roleMemberships.entity', 'permissions')
            ->findOrFail($role_id);

        $user = auth()->user();
        $service = $this->baseRoleService->for($role);

        $canEdit = $user->can('superuser') || $user->can('administrate access control groups');
        $isMember = $role->roleMemberships()
            ->where('entity_type', User::class)
            ->where('entity_id', $user->getAuthIdentifier())
            ->exists();

        // The read-only detail backs the discover flow, so members and eligible users may view it
        // too — not only admins/moderators. (canJoin() == meetsCriteria() for on-request/opt-in.)
        $canView = $canEdit || $service->canModerate($user) || $isMember || $service->canJoin($user);

        abort_unless($canView, 403);

        return Inertia::render('AccessControl/RoleDetail', [
            'role' => (new RoleDetailResource($role))->resolve(),
            'can_edit' => $canEdit,
            'activeSidebarElement' => 'acl.groups',
            'pageTranslations' => Translations::gather(['web::access_control']),
        ]);
    }
}
