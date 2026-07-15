<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\RoleRessource;
use Seatplus\Web\Support\Translations;

class ShowControlGroupsController extends Controller
{
    public function __invoke(): Response
    {
        $user = auth()->user();
        $userId = $user->getAuthIdentifier();

        // The corps/alliances the user belongs to — used to find groups they're eligible to join.
        $characters = $user->characters;
        $corporationIds = $characters->pluck('corporation_id')->filter()->unique()->values()->all();
        $allianceIds = $characters->pluck('alliance_id')->filter()->unique()->values()->all();

        // My groups: any role where the user has a membership row (member, applicant, or moderator).
        $myGroups = Role::query()
            ->whereHas('roleMemberships', fn (Builder $query) => $query
                ->where('entity_type', User::class)
                ->where('entity_id', $userId))
            ->get();

        // Available: self-service / request groups the user is eligible for (a criteria corp/alliance
        // matches one of their characters — mirrors meetsCriteria()) and is not already in.
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

        return Inertia::render('AccessControl/ControlGroupsIndex', [
            'myGroups' => RoleRessource::collection($myGroups)->resolve(),
            'availableGroups' => RoleRessource::collection($availableGroups)->resolve(),
            'canCreate' => $user->can('superuser') || $user->can('administrate access control groups'),
            'activeSidebarElement' => 'acl.groups',
            'pageTranslations' => Translations::gather(['web::access_control']),
        ]);
    }
}
