<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\Permissions\Affiliation;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Controller;

class ShowControlGroupController extends Controller
{
    private const ENTITY_TYPE_MAP = [
        CorporationInfo::class => 'corporation',
        AllianceInfo::class => 'alliance',
        CharacterInfo::class => 'character',
    ];

    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(int $role_id): Response|RedirectResponse
    {
        $role = Role::with(
            'affiliations.affiliatable',
            'role_memberships.entity.main_character',
            'permissions',
        )->findOrFail($role_id);

        $user = auth()->user();

        $canEdit = $user->can('superuser') || $user->can('administrate access control groups');
        $canView = $canEdit || $this->baseRoleService->for($role)->canModerate($user);

        abort_unless($canView, 403);

        $userMemberships = $role->role_memberships->where('entity_type', User::class);

        return Inertia::render('AccessControl/RoleDetail', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'type' => $role->type->value,
                'affiliations' => $role->affiliations->map(fn (Affiliation $affiliation) => [
                    'id' => $affiliation->affiliatable_id,
                    'entity_type' => self::ENTITY_TYPE_MAP[$affiliation->affiliatable_type] ?? 'character',
                    'affiliation_type' => $affiliation->type,
                ]),
                'permissions' => $role->permissions->pluck('name'),
                'members' => $userMemberships->map(fn (RoleMembership $membership) => [
                    'id' => $membership->entity_id,
                    'status' => $membership->status,
                    'can_moderate' => $membership->can_moderate,
                    'user' => $membership->entity,
                ]),
                'moderators' => $userMemberships
                    ->where('can_moderate', true)
                    ->map(fn (RoleMembership $membership) => $membership->entity)
                    ->values(),
            ],
            'can_edit' => $canEdit,
            'activeSidebarElement' => 'acl.groups',
        ]);
    }
}
