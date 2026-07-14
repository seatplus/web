<?php

namespace Seatplus\Web\Http\Actions\Character\Asset;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Eveapi\Models\Universe\Structure;
use Seatplus\Web\Services\Query\AssetSearchScope;
use Seatplus\Web\Services\Query\LocationWatchListScope;
use Seatplus\Web\Services\Query\TypeWatchListScope;

/**
 * Paginates the LOCATION SHELLS a character's (filtered) assets live in — no asset trees. Which
 * locations appear is a flat, indexed `whereHas('descendantAssets', …)` (matches at any nesting
 * depth). Each location's items are loaded lazily, per-location, by AssetsController::location()
 * when the location scrolls into view.
 */
class GetCharacterAssetLocationAction
{
    private array $validated = [];

    public function execute(array $validated): LengthAwarePaginator
    {
        $this->validated = $validated;

        $results = $this->getPaginatedLocations();
        $locationCollection = $results->getCollection();
        $total = $results->total();

        // Asset safety is a virtual location (no universe_locations row) so the Location query never
        // selects it — prepend it as a shell on page 1 when a matching asset is rooted there.
        if ($results->currentPage() === 1 && $this->hasMatchingAssetSafety()) {
            $locationCollection->prepend(new Location(['location_id' => Asset::ASSET_SAFETY]));

            if ($locationCollection->count() > $total) {
                $total = $locationCollection->count();
            }
        }

        return new LengthAwarePaginator(
            $locationCollection,
            $total,
            $results->perPage(),
            $results->currentPage(),
            ['path' => request()->url(), 'query' => request()->query(), 'pageName' => 'assets']
        );
    }

    public function getPaginatedLocations(): LengthAwarePaginator
    {
        return Location::query()
            ->with(['locatable' => ['system']])
            // Flat, indexed selection via root_location_id — a location appears if it holds a
            // matching asset at any depth. No asset tree is loaded here.
            ->whereHas('descendantAssets', $this->getAssetQuery())
            ->when(
                data_get($this->validated, 'only_unknown_locations'),
                fn (Builder $query) => $query
                    ->doesntHaveMorph('locatable', [Station::class, Structure::class])
                    ->orWhereNull('locatable_type')
            )
            ->tap(new LocationWatchListScope($this->validated))
            ->orderBy('location_id')
            ->paginate(pageName: 'assets');
    }

    private function getAssetQuery(): \Closure
    {
        $character_ids = $this->validated['character_ids'];

        return fn (Builder $query) => $query
            ->whereIn('assetable_id', $character_ids)
            ->where('assetable_type', CharacterInfo::class)
            ->tap(new AssetSearchScope($this->validated))
            ->tap(new TypeWatchListScope($this->validated));
    }

    private function hasMatchingAssetSafety(): bool
    {
        return Asset::query()
            ->where('root_location_id', Asset::ASSET_SAFETY)
            ->tap($this->getAssetQuery())
            ->exists();
    }
}
