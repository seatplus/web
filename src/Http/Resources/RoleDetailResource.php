<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\Permissions\Affiliation;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Services\Roles\AbstractRoleService;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Support\AccessControl\RoleTypeMetadata;

/**
 * Full role payload for the detail / configure / manage pages. Extends the list resource's
 * per-user capability flags with the two clearly-separated concepts:
 *   - `applies_to`  = the entities the group's permissions cover (affiliations; NOT membership).
 *   - `eligibility` = the corps/alliances whose members may join / are auto-added (criteria).
 *
 * Expects the role loaded with `affiliations.affiliatable`, `roleMemberships.entity`, `permissions`.
 *
 * @mixin Role
 */
class RoleDetailResource extends JsonResource
{
    private const array ENTITY_TYPE_MAP = [
        CorporationInfo::class => 'corporation',
        AllianceInfo::class => 'alliance',
        CharacterInfo::class => 'character',
    ];

    public function toArray(Request $request): array
    {
        return array_merge(
            (new RoleRessource($this->resource))->toArray($request),
            [
                'capabilities' => RoleTypeMetadata::for($this->type),
                'permissions' => $this->permissions->pluck('name'),
                // Authorization scope — what the permissions apply to. The three affiliation types are
                // independent. "Everything" = the Doomheim sentinel affiliated as INVERSE.
                'applies_to' => [
                    'everything' => $this->hasEverythingAffiliation(),
                    'allowed' => $this->mapAffiliations([AffiliationType::ALLOWED->value]),
                    'inverse' => $this->mapAffiliations([AffiliationType::INVERSE->value]),
                    'excluded' => $this->mapAffiliations([AffiliationType::FORBIDDEN->value]),
                ],
                // Membership eligibility — the corp/alliance criteria rows. "Anyone" = the Doomheim
                // sentinel criterion (open to all).
                'eligibility' => [
                    'anyone' => $this->isOpenToAll(),
                    'entities' => $this->mapCriteria(),
                ],
            ]
        );
    }

    /** Whether the group's permissions apply to everyone (Doomheim affiliated as INVERSE). */
    private function hasEverythingAffiliation(): bool
    {
        return $this->affiliations->contains(
            fn (Affiliation $affiliation) => (int) $affiliation->affiliatable_id === AbstractRoleService::EVERYONE_CORPORATION_ID
                && $affiliation->affiliatable_type === CorporationInfo::class
                && $affiliation->type === AffiliationType::INVERSE->value
        );
    }

    /** Whether the group is open to all (Doomheim present as a criterion). */
    private function isOpenToAll(): bool
    {
        return $this->roleMemberships->contains(
            fn (RoleMembership $membership) => (int) $membership->entity_id === AbstractRoleService::EVERYONE_CORPORATION_ID
                && $membership->entity_type === CorporationInfo::class
        );
    }

    /**
     * @param  list<string>  $types
     * @return array<int, array{id: int, entity_type: string, name: string|null}>
     */
    private function mapAffiliations(array $types): array
    {
        return $this->affiliations
            ->whereIn('type', $types)
            ->reject(fn (Affiliation $affiliation) => (int) $affiliation->affiliatable_id === AbstractRoleService::EVERYONE_CORPORATION_ID
                && $affiliation->affiliatable_type === CorporationInfo::class)
            ->map(function (Affiliation $affiliation): array {
                $name = data_get($affiliation->affiliatable, 'name');

                return [
                    'id' => (int) $affiliation->affiliatable_id,
                    'entity_type' => self::ENTITY_TYPE_MAP[$affiliation->affiliatable_type] ?? 'character',
                    'name' => is_string($name) ? $name : null,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * The criteria rows (corp/alliance memberships) that gate eligibility.
     *
     * @return array<int, array{id: int, entity_type: string, name: string|null}>
     */
    private function mapCriteria(): array
    {
        return $this->roleMemberships
            ->whereIn('entity_type', [CorporationInfo::class, AllianceInfo::class])
            ->reject(fn (RoleMembership $membership) => (int) $membership->entity_id === AbstractRoleService::EVERYONE_CORPORATION_ID
                && $membership->entity_type === CorporationInfo::class)
            ->map(function (RoleMembership $membership): array {
                $name = data_get($membership->entity, 'name');

                return [
                    'id' => (int) $membership->entity_id,
                    'entity_type' => self::ENTITY_TYPE_MAP[$membership->entity_type] ?? 'corporation',
                    'name' => is_string($name) ? $name : null,
                ];
            })
            ->values()
            ->all();
    }
}
