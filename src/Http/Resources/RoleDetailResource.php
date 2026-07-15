<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\Permissions\Affiliation;
use Seatplus\Auth\Models\Permissions\Role;
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
                // Authorization scope — what the permissions apply to.
                'applies_to' => [
                    'mode' => $this->affiliations->contains('type', AffiliationType::INVERSE->value)
                        ? 'everyone_except'
                        : 'only_these',
                    'included' => $this->mapAffiliations([AffiliationType::ALLOWED->value, AffiliationType::INVERSE->value]),
                    'excluded' => $this->mapAffiliations([AffiliationType::FORBIDDEN->value]),
                ],
                // Membership eligibility — the corp/alliance criteria rows.
                'eligibility' => $this->mapCriteria(),
            ]
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
