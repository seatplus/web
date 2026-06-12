<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Models\Permissions\Affiliation;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Controller;

class ShowControlGroupController extends Controller
{
    private const array ENTITY_TYPE_MAP = [
        CorporationInfo::class => 'corporation',
        AllianceInfo::class => 'alliance',
        CharacterInfo::class => 'character',
    ];

    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(int $role_id): Response|RedirectResponse
    {
        $role = Role::with('affiliations.affiliatable', 'roleMemberships.entity', 'permissions')
            ->findOrFail($role_id);

        $user = auth()->user();

        $canEdit = $user->can('superuser') || $user->can('administrate access control groups');
        $canView = $canEdit || $this->baseRoleService->for($role)->canModerate($user);

        abort_unless($canView, 403);

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
            ],
            'can_edit' => $canEdit,
            'activeSidebarElement' => 'acl.groups',
        ]);
    }
}
