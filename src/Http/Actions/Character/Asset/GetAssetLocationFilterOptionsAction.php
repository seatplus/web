<?php

namespace Seatplus\Web\Http\Actions\Character\Asset;

use Illuminate\Database\Eloquent\Builder;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Universe\Location;

/**
 * The distinct regions and systems the given characters actually have assets in — used to populate
 * the assets region/system filters as a multiselect of only the relevant options (instead of a
 * free-text search over every region/system in EVE).
 */
class GetAssetLocationFilterOptionsAction
{
    /**
     * @param  list<int>  $characterIds
     * @return array{regions: list<array{id: int, name: string}>, systems: list<array{id: int, name: string}>}
     */
    public function execute(array $characterIds): array
    {
        $locations = Location::query()
            ->whereHas('descendantAssets', fn (Builder $query) => $query
                ->whereIn('assetable_id', $characterIds)
                ->where('assetable_type', CharacterInfo::class))
            ->with('locatable.system.region')
            ->get();

        $systems = $locations
            ->map(fn (Location $location) => data_get($location, 'locatable.system'))
            ->filter()
            ->unique('system_id')
            ->map(fn ($system): array => ['id' => (int) $system->system_id, 'name' => $system->name])
            ->sortBy('name')
            ->values()
            ->all();

        $regions = $locations
            ->map(fn (Location $location) => data_get($location, 'locatable.system.region'))
            ->filter()
            ->unique('region_id')
            ->map(fn ($region): array => ['id' => (int) $region->region_id, 'name' => $region->name])
            ->sortBy('name')
            ->values()
            ->all();

        return ['regions' => $regions, 'systems' => $systems];
    }
}
