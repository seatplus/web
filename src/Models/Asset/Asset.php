<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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

    public function scopeInRegion(Builder $query, int | array $regions): Builder
    {
        $region_ids = is_array($regions) ?: [$regions];

        return $query->whereHas('location', function (Builder $query) use ($region_ids) {
            $query->whereHasMorph(
                'locatable',
                '*',
                function (Builder $query) use ($region_ids) {
                    $query->whereHas('system.region', function (Builder $query) use ($region_ids) {
                        $query->whereIn('universe_regions.region_id', $region_ids);
                    });
                });
        })->orWhereHas('manual_location', fn (Builder $query) => $query
            ->whereHas('system.region', fn (Builder $query) => $query->whereIn('universe_regions.region_id', $region_ids))
        );
    }

    public function scopeInSystems(Builder $query, int | array $systems): Builder
    {
        $system_ids = is_array($systems) ?: [$systems];

        return $query->whereHas('location', fn (Builder $query) => $query
            ->whereHasMorph(
                'locatable',
                '*',
                fn (Builder $query) => $query
                    ->whereHas('system', fn (Builder $query) => $query
                        ->whereIn('universe_systems.system_id', $system_ids)
                    )
            )
        )->orWhereHas('manual_location', fn (Builder $query) => $query
            ->whereIn('manual_locations.solar_system_id', $system_ids)
        );
    }
}
