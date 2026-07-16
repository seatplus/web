<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Enums\RoleMembershipStatus;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\RoleDetailResource;
use Seatplus\Web\Http\Resources\RoleMemberResource;
use Seatplus\Web\Support\AccessControl\AssignablePermissions;
use Seatplus\Web\Support\AccessControl\RoleTypeMetadata;
use Seatplus\Web\Support\Translations;

/**
 * Unified role hub — the discover/configure/moderate surfaces of a single role on one page,
 * as an alternative to the separate per-action pages. Tabs are gated exactly like the individual
 * pages: everyone who may view the role sees Overview; moderators/admins see Members; admins see
 * Configure. The heavier member/permission payloads are only sent when the viewer can use them.
 */
class RoleHubController extends Controller
{
    private const array TABS = ['overview', 'members', 'configure'];

    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(Request $request, int $role_id): Response
    {
        $role = Role::with([
            'affiliations.affiliatable',
            'permissions',
            'roleMemberships.entity' => fn (MorphTo $morph) => $morph->morphWith([User::class => ['mainCharacter', 'characters']]),
        ])->findOrFail($role_id);

        $user = auth()->user();
        $service = $this->baseRoleService->for($role);

        $canEdit = $user->can('superuser') || $user->can('administrate access control groups');
        $canModerate = $service->canModerate($user);

        $userMemberships = $role->roleMemberships->where('entity_type', User::class);
        $isMember = $role->roleMemberships()
            ->where('entity_type', User::class)
            ->where('entity_id', $user->getAuthIdentifier())
            ->exists();

        // Same view gate as the standalone detail page: members and eligible users may open the hub
        // (they only get the Overview tab); the other tabs are gated below.
        abort_unless($canEdit || $canModerate || $isMember || $service->canJoin($user), 403);

        // Members tab is for moderators/admins; the Moderators panel within it is admins-only.
        $canManageMembers = $canEdit || $canModerate;

        return Inertia::render('AccessControl/RoleHub', [
            'role' => (new RoleDetailResource($role))->resolve(),
            'initialTab' => in_array($request->query('tab'), self::TABS, true) ? $request->query('tab') : 'overview',
            'canManageMembers' => $canManageMembers,
            'canConfigure' => $canEdit,
            'canManageModerators' => $canEdit,
            // Member lists — only when the viewer can act on them.
            'members' => $canManageMembers ? RoleMemberResource::collection(
                $userMemberships->where('status', RoleMembershipStatus::ACTIVE->value)
            )->resolve() : [],
            'applicants' => $canManageMembers ? RoleMemberResource::collection(
                $userMemberships->where('status', RoleMembershipStatus::PENDING->value)
            )->resolve() : [],
            'moderators' => $canManageMembers ? RoleMemberResource::collection(
                $userMemberships->where('can_moderate', true)
            )->resolve() : [],
            // Configure inputs — admins only.
            'joinMethods' => $canEdit ? RoleTypeMetadata::all() : [],
            'availablePermissions' => $canEdit ? AssignablePermissions::all() : [],
            'activeSidebarElement' => 'acl.hub',
            'pageTranslations' => Translations::gather(['web::access_control']),
        ]);
    }
}
