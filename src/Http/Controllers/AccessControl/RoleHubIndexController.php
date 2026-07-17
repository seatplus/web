<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\RoleRessource;
use Seatplus\Web\Support\Translations;

/**
 * Index for the unified role hub — the same segmented discover list as {@see ShowControlGroupsController},
 * but its cards route into the single-page {@see RoleHubController} instead of the separate per-action
 * pages. Kept as a parallel surface so the two designs can be compared side by side.
 */
class RoleHubIndexController extends Controller
{
    public function __invoke(): Response
    {
        $user = auth()->user();
        $userId = $user->getAuthIdentifier();

        // The hub index is reachable by anyone who has a reason to see groups: superusers and global
        // ACL admins, users who may view access control, and per-role moderators (who manage a group
        // without necessarily holding the view permission). superuser bypasses the can() checks.
        $isModerator = RoleMembership::query()
            ->where('entity_type', User::class)
            ->where('entity_id', $userId)
            ->where('can_moderate', true)
            ->exists();

        abort_unless(
            $user->can('view access control')
            || $user->can('administrate access control groups')
            || $isModerator,
            403
        );

        // Eager-load characterAffiliation: the corporation_id/alliance_id accessors read it, and
        // lazy loading is disabled (strict mode).
        $characters = $user->characters()->with('characterAffiliation')->get();
        $corporationIds = $characters->pluck('corporation_id')->filter()->unique()->values()->all();
        $allianceIds = $characters->pluck('alliance_id')->filter()->unique()->values()->all();

        $myGroups = Role::query()
            ->whereHas('roleMemberships', fn (Builder $query) => $query
                ->where('entity_type', User::class)
                ->where('entity_id', $userId))
            ->get();

        $canManage = $user->can('superuser') || $user->can('administrate access control groups');

        $allGroups = $canManage
            ? Role::query()->whereNotIn('id', $myGroups->modelKeys())->get()
            : collect();

        $availableGroups = Role::query()
            ->whereIn('type', [RoleType::OPT_IN->value, RoleType::ON_REQUEST->value])
            ->where(fn (Builder $query) => $query
                ->when($corporationIds !== [], fn (Builder $query) => $query->orWhereHas('roleMemberships', fn (Builder $query) => $query
                    ->where('entity_type', CorporationInfo::class)
                    ->whereIn('entity_id', $corporationIds)))
                ->when($allianceIds !== [], fn (Builder $query) => $query->orWhereHas('roleMemberships', fn (Builder $query) => $query
                    ->where('entity_type', AllianceInfo::class)
                    ->whereIn('entity_id', $allianceIds))))
            ->whereDoesntHave('roleMemberships', fn (Builder $query) => $query
                ->where('entity_type', User::class)
                ->where('entity_id', $userId))
            ->get();

        return Inertia::render('AccessControl/RoleHubIndex', [
            'myGroups' => RoleRessource::collection($myGroups)->resolve(),
            'availableGroups' => RoleRessource::collection($availableGroups)->resolve(),
            'allGroups' => RoleRessource::collection($allGroups)->resolve(),
            'canCreate' => $canManage,
            'canManage' => $canManage,
            'activeSidebarElement' => 'acl.hub',
            'pageTranslations' => Translations::gather(['web::access_control']),
        ]);
    }
}
