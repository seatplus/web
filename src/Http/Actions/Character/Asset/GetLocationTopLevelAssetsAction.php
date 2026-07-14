<?php

namespace Seatplus\Web\Http\Actions\Character\Asset;

use Illuminate\Pagination\LengthAwarePaginator;
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Services\Query\AssetSearchScope;
use Seatplus\Web\Services\Query\TypeWatchListScope;

/**
 * One location's TOP-LEVEL items, paginated — loaded lazily per-location when the location scrolls
 * into view.
 *
 *  - filtered: the top-level items that match or CONTAIN a match at any depth, resolved from the
 *    denormalized root_item_id in a single flat query (no tree eager-load, no PHP recursion):
 *    `WHERE item_id IN (SELECT DISTINCT root_item_id WHERE root_location_id = :loc AND <filter>)`.
 *  - unfiltered: the location's direct children (`location_id = :loc`).
 *
 * `character_ids` must already be the authorised set (AssetsController scopes it via getCharacterIds);
 * every query is hard-scoped to it, so an arbitrary location_id can only ever return authorised assets.
 */
class GetLocationTopLevelAssetsAction
{
    public function execute(int $locationId, array $validated): LengthAwarePaginator
    {
        $characterIds = $validated['character_ids'];

        $query = Asset::query()
            ->whereIn('assetable_id', $characterIds)
            ->where('assetable_type', CharacterInfo::class);

        if ($this->isFiltered($validated)) {
            // Top-level items whose subtree contains a match — a matching asset at any depth rolls
            // up to its top-level container via root_item_id. Kept as a subquery so a broad filter
            // over a huge location never pulls the matching id set into PHP.
            $matchingTopLevelIds = Asset::query()
                ->select('root_item_id')
                ->distinct()
                ->whereIn('assetable_id', $characterIds)
                ->where('assetable_type', CharacterInfo::class)
                ->where('root_location_id', $locationId)
                ->tap(new AssetSearchScope($validated))
                ->tap(new TypeWatchListScope($validated));

            $query->whereIn('item_id', $matchingTopLevelIds);
        } else {
            // Unfiltered: the location's direct children (top-level items).
            $query->where('location_id', $locationId);
        }

        return $query
            ->with('type.group')
            ->withCount('content')
            ->orderBy('item_id')
            ->paginate(pageName: 'items');
    }

    private function isFiltered(array $validated): bool
    {
        return filled(data_get($validated, 'search'))
            || filled(data_get($validated, 'types'))
            || filled(data_get($validated, 'groups'))
            || filled(data_get($validated, 'categories'));
    }
}
