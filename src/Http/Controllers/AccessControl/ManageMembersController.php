<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Enums\RoleMembershipStatus;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\RoleMemberResource;
use Seatplus\Web\Support\AccessControl\RoleTypeMetadata;
use Seatplus\Web\Support\Translations;

class ManageMembersController extends Controller
{
    public function __invoke(int $role_id): Response
    {
        $role = Role::findOrFail($role_id);

        $user = auth()->user();

        // Moderators are managed by admins only; a plain moderator manages members/applications.
        $canManageModerators = $user->can('superuser') || $user->can('administrate access control groups');
        $canModerate = (new BaseRoleService)->for($role)->canModerate($user);

        // Admins (superuser / global ACL admin) and per-role moderators may open the manage page.
        abort_unless($canManageModerators || $canModerate, 403);

        $memberships = $role->roleMemberships()
            ->where('entity_type', User::class)
            ->with(['entity' => fn (MorphTo $morph) => $morph->morphWith([User::class => ['mainCharacter']])])
            ->get();

        return Inertia::render('AccessControl/ModerateMembers', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'type' => $role->type->value,
                'type_label' => RoleTypeMetadata::for($role->type)['label'],
                'capabilities' => RoleTypeMetadata::for($role->type),
            ],
            'members' => RoleMemberResource::collection(
                $memberships->where('status', RoleMembershipStatus::ACTIVE->value)
            )->resolve(),
            'applicants' => RoleMemberResource::collection(
                $memberships->where('status', RoleMembershipStatus::PENDING->value)
            )->resolve(),
            'moderators' => RoleMemberResource::collection(
                $memberships->where('can_moderate', true)
            )->resolve(),
            'canManageModerators' => $canManageModerators,
            'activeSidebarElement' => 'acl.groups',
            'pageTranslations' => Translations::gather(['web::access_control']),
        ]);
    }
}
