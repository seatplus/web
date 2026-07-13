<?php

namespace Seatplus\Web\Http\Actions\Character\Asset;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Eveapi\Models\Universe\Structure;
use Seatplus\Web\Services\Query\AssetSearchScope;
use Seatplus\Web\Services\Query\LocationWatchListScope;
use Seatplus\Web\Services\Query\TypeWatchListScope;

class GetCharacterAssetLocationAction
{
    const array ASSETRELATIONS = [
        'type' => [
            'group',
        ],
        'content' => [
            'content',
        ],
    ];

    private array $validated = [];

    public function execute(array $validated): LengthAwarePaginator
    {
        $this->validated = $validated;

        $results = $this->getPaginatedLocations();

        $locationCollection = $results->getCollection();

        $total = $results->total();

        // if currentPage is 1 add asset safety location at beginning
        if ($results->currentPage() === 1) {
            $this->addAssetSafety($locationCollection);

            // if $locationCollection is larger than the total, we need to adjust the total
            if ($locationCollection->count() > $total) {
                $total = $locationCollection->count();
            }
        }

        // Prune to matching branches only when an asset-level filter is active; unfiltered loads
        // top-level items only (no content relation) so there is nothing to prune.
        if ($this->isFiltered()) {
            $locationCollection = $this->filterLocationAssets($locationCollection);
        }

        return new LengthAwarePaginator(
            $locationCollection,
            $total,
            $results->perPage(),
            $results->currentPage(),
            ['path' => request()->url(), 'query' => request()->query(), 'pageName' => 'assets']
        );
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

    private function filterAsset(Collection|Asset|null $asset): bool
    {
        if (! $asset) {
            return false;
        }

        // if $query is an Asset, wrap it in a collection
        if ($asset instanceof Asset) {
            $asset = collect([$asset]);
        }

        return $asset
            ->filter(new AssetSearchScope($this->validated))
            ->filter(new TypeWatchListScope($this->validated))
            ->isNotEmpty();
    }

    public function filterAssets(Collection $assets): Collection
    {
        return $assets->filter(fn (Asset $asset) => $this->filterAssetsLogic($asset))
            ->map(function (Asset $asset) {
                if ($asset->relationLoaded('content') && $asset->content->isNotEmpty()) {
                    $filtered_content = $this->filterContent($asset->content);
                    $asset->setRelation('content', $filtered_content);
                }

                return $asset;
            })
            ->values();
    }

    private function filterAssetsLogic(Asset $asset): bool
    {
        return $this->filterAsset($asset) || $this->filterAsset(data_get($asset, 'content')) || $this->filterAsset(data_get($asset, 'content.content'));
    }

    private function filterContent(Collection $content): Collection
    {
        return $content->filter(fn (Asset $asset) => $this->filterAssetsLogic($asset))
            ->map(function (Asset $asset) {
                if ($asset->relationLoaded('content')) {
                    $filtered_content = $this->filterContent($asset->content);
                    $asset->setRelation('content', $filtered_content);
                }

                return $asset;
            });
    }

    public function addAssetSafety(Collection $locationCollection): void
    {
        $asset_safety = Asset::where('location_id', Asset::ASSET_SAFETY)
            ->with(self::ASSETRELATIONS)
            ->get();

        if ($asset_safety->isNotEmpty()) {
            $asset_safety_location = new Location([
                'location_id' => Asset::ASSET_SAFETY,
            ]);

            $asset_safety_location->setRelation('assets', $asset_safety);
            $locationCollection->prepend($asset_safety_location);
        }
    }

    public function getPaginatedLocations(): LengthAwarePaginator
    {
        $character_ids = $this->validated['character_ids'];

        $assetScope = fn (Relation $query) => $query
            ->whereIn('assetable_id', $character_ids)
            ->where('assetable_type', CharacterInfo::class);

        return Location::query()
            ->with(['locatable' => ['system']])
            ->when(
                $this->isFiltered(),
                // Filtered: load the nested tree so a matched location can be pruned to only the
                // top-level items that are, or contain, a match (filterLocationAssets in execute()).
                fn (Builder $query) => $query->with([
                    'assets' => fn (Relation $q) => $assetScope($q)->with(self::ASSETRELATIONS),
                ]),
                // Unfiltered (the common case): only top-level items + a contents count for the
                // chevron. No content.content eager-load, no PHP tree recursion.
                fn (Builder $query) => $query->with([
                    'assets' => fn (Relation $q) => $assetScope($q)->with('type.group')->withCount('content'),
                ]),
            )
            // Location selection is always flat via root_location_id (matches at any nesting depth).
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

    /**
     * Whether an asset-level filter (search / type / group / category) is active. Region/system are
     * location-level (LocationWatchListScope) and don't require loading/pruning the asset tree.
     */
    private function isFiltered(): bool
    {
        return filled(data_get($this->validated, 'search'))
            || filled(data_get($this->validated, 'types'))
            || filled(data_get($this->validated, 'groups'))
            || filled(data_get($this->validated, 'categories'));
    }

    public function filterLocationAssets(Collection $locationCollection): Collection
    {
        return $locationCollection
            ->map(function (Location $location) {
                $filtered_assets = $this->filterAssets($location->assets);
                $location->setRelation('assets', $filtered_assets);

                return $location;
            });
    }
}
