<?php

namespace Seatplus\Web\Http\Actions\Shared;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Resources\CharacterInfoRessource;
use Seatplus\Web\Http\Resources\CorporationInfoRessource;
use Seatplus\Web\Services\GetAffiliatedIds;
use Seatplus\Web\Services\GetRecruitIdsService;

/**
 * Single source of truth for the entity-picker's affiliated character/corporation lists. Both the
 * standalone GetAffiliated{Characters,Corporations} JSON endpoints (still used by the recruitment
 * corporation list, the enlistment config and the character filter modal) and the lazily-resolved
 * `affiliatedEntities` Inertia shared prop (the slide-over picker, loaded via WhenVisible) delegate
 * here so the affiliation query + Resource shaping live in exactly one place.
 */
class GetAffiliatedEntitiesAction
{
    public function __construct(
        private readonly GetAffiliatedIds $getAffiliatedIds,
    ) {}

    public function characters(string $permission, ?string $search = null): AnonymousResourceCollection
    {
        $affiliatedIds = $this->getAffiliatedIds->get($permission);
        $ownedCharacterIds = auth()->user()->characters->pluck('character_id')->toArray();
        $recruitIds = GetRecruitIdsService::get();

        $query = $this->characterInfoQuery($affiliatedIds, $search)
            ->union($this->characterInfoQuery($recruitIds, $search))
            ->union($this->characterInfoQuery($ownedCharacterIds, $search))
            ->distinct()
            ->with(['corporation', 'alliance'])
            ->has($permission)
            ->get();

        return CharacterInfoRessource::collection($query);
    }

    /**
     * @param  array<int, string>  $corporationRoles
     */
    public function corporations(string $permission, array $corporationRoles = [], ?string $search = null): AnonymousResourceCollection
    {
        $affiliatedIds = $this->getAffiliatedIds->get($permission, $corporationRoles);

        $query = CorporationInfo::query()
            ->whereIn('corporation_id', $affiliatedIds)
            ->select('corporation_infos.*')
            ->when($search, fn (Builder $query) => $query->where('name', 'like', "%{$search}%"))
            ->with('alliance')
            ->get();

        return CorporationInfoRessource::collection($query);
    }

    private function characterInfoQuery(array $ids, ?string $search = null): Builder
    {
        return CharacterInfo::query()
            ->whereIn('character_id', $ids)
            ->when($search, fn (Builder $query) => $query->where('character_infos.name', 'like', "%{$search}%"))
            ->orderBy('character_infos.name');
    }
}
