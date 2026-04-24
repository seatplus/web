<?php

namespace Seatplus\Web\Http\Actions\Character\Asset;

use Illuminate\Database\Eloquent\Collection;
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
    const ASSETRELATIONS = [
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

        $locationCollection = $this->filterLocationAssets($locationCollection);

        return new LengthAwarePaginator(
            $locationCollection,
            $total,
            $results->perPage(),
            $results->currentPage(),
            ['path' => request()->url(), 'query' => request()->query()]
        );

    }

    private function getAssetQuery(): \Closure
    {
        $character_ids = $this->validated['character_ids'];

        return fn (mixed $query) => $query
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

    public function filterAssets(mixed $assets): Collection
    {
        return $assets->filter(fn (mixed $asset) => $this->filterAssetsLogic($asset))
            ->map(function (mixed $asset) {
                if ($asset->content) {
                    $filtered_content = $this->filterContent($asset->content);
                    $asset->setRelation('content', $filtered_content);
                }

                return $asset;
            })
            ->values();
    }

    private function filterAssetsLogic(mixed $asset): bool
    {
        return $this->filterAsset($asset) || $this->filterAsset(data_get($asset, 'content')) || $this->filterAsset(data_get($asset, 'content.content'));
    }

    private function filterContent(mixed $content): Collection
    {
        return $content->filter(fn (mixed $asset) => $this->filterAssetsLogic($asset))
            ->map(function (mixed $asset) {
                if ($asset->relationLoaded('content') && $asset->content) {
                    $filtered_content = $this->filterContent($asset->content);
                    $asset->setRelation('content', $filtered_content);
                }

                return $asset;
            });
    }

    public function addAssetSafety(mixed $locationCollection): void
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

    public function getPaginatedLocations(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $character_ids = $this->validated['character_ids'];

        return Location::query()
            ->with([
                'assets' => self::ASSETRELATIONS,
                'locatable' => [
                    'system',
                ],
            ])
            ->with('assets', fn (mixed $query) => $query->whereIn('assetable_id', $character_ids)->where('assetable_type', CharacterInfo::class))
            ->where(
                fn (mixed $query) => $query
                    ->whereHas('assets', $this->getAssetQuery())
                    ->orWhereHas('assets.content', $this->getAssetQuery())
                    ->orWhereHas('assets.content.content', $this->getAssetQuery())
            )
            ->when(
                data_get($this->validated, 'only_unknown_locations'),
                fn (mixed $query) => $query
                    ->doesntHaveMorph('locatable', [Station::class, Structure::class])
                    ->orWhereNull('locatable_type')
            )
            ->tap(new LocationWatchListScope($this->validated))
            ->orderBy('location_id')
            ->paginate();
    }

    public function filterLocationAssets(mixed $locationCollection): mixed
    {
        return $locationCollection
            ->map(function (mixed $location) {
                $filtered_assets = $this->filterAssets($location->assets);
                $location->setRelation('assets', $filtered_assets);

                return $location;
            });
    }
}
