<?php


namespace Seatplus\Web\Models\Asset;


use Illuminate\Database\Eloquent\Builder;
use Seatplus\Eveapi\Models\Assets\Asset as EveApiAsset;
use Seatplus\Web\Models\ManualLocation;

class Asset extends EveApiAsset
{

    public function manual_location()
    {
        return $this->hasOne(ManualLocation::class, 'location_id', 'location_id')
            ->latest();
    }

    public function scopeInRegion(Builder $query, int|array $regions): Builder
    {

        $region_ids = is_array($regions) ? $regions : [$regions];

        return $query->whereHas('location', function (Builder $query) use ($region_ids) {
            $query->whereHasMorph(
                'locatable',
                '*',
                function (Builder $query) use ($region_ids) {
                    $query->whereHas('system.region', function (Builder $query) use ($region_ids) {
                        $query->whereIn('universe_regions.region_id', $region_ids);
                    });
                });
        })->orWhereHas('manual_location', fn(Builder $query) => $query
            ->whereHas('system.region', fn(Builder $query) => $query->whereIn('universe_regions.region_id', $region_ids))
        );
    }

    public function scopeInSystems(Builder $query, int|array $systems): Builder
    {

        $system_ids = is_array($systems) ? $systems : [$systems];

        return $query->whereHas('location', fn (Builder $query) => $query
            ->whereHasMorph(
                'locatable',
                '*',
                fn (Builder $query) => $query
                    ->whereHas('system', fn (Builder $query) => $query
                        ->whereIn('universe_systems.system_id', $system_ids)
                    )
            )
        )->orWhereHas('manual_location', fn(Builder $query) => $query
            ->whereIn('manual_locations.solar_system_id', $system_ids)
        );
    }
}
