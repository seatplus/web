<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
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

namespace Seatplus\Web\Http\Actions\Corporation\Recruitment;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Seatplus\Eveapi\Jobs\Universe\ResolveUniverseRegionByRegionIdJob;
use Seatplus\Eveapi\Jobs\Universe\ResolveUniverseSystemBySystemIdJob;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Region;
use Seatplus\Eveapi\Models\Universe\System;
use Seatplus\Eveapi\Models\Universe\Type;
use Seatplus\Web\Models\Recruitment\Enlistment;

class UpdateWatchlistAction
{
    private Enlistment $enlistment;
    private array $validatedData;

    public function execute(int $corporation_id, array $validated_data)
    {
        $this->enlistment = Enlistment::find($corporation_id);
        $this->validatedData = $validated_data;

        $this->handleSystems();
        $this->handleRegions();
        $this->handleItems();
    }

    private function handleSystems()
    {
        if (!Arr::has($this->validatedData, 'systems')) {
            // return early if systems is not part of the validated data, f.e. when updating items only
            return;
        }

        $system_ids = data_get($this->validatedData, 'systems.*.id', []);
        $this->enlistment->systems()->sync($system_ids);

        collect($system_ids)
            ->diff(System::whereIn('system_id', $system_ids)->select('system_id')->pluck('system_id'))
            ->each(fn ($system_id) => ResolveUniverseSystemBySystemIdJob::dispatchSync($system_id));
    }

    private function handleRegions()
    {
        if (!Arr::has($this->validatedData, 'regions')) {
            // return early if systems is not part of the validated data, f.e. when updating items only
            return;
        }

        $region_ids = data_get($this->validatedData, 'regions.*.id', []);
        $this->enlistment->regions()->sync($region_ids);

        collect($region_ids)
            ->diff(Region::whereIn('region_id', $region_ids)->select('region_id')->pluck('region_id'))
            ->each(fn ($region_id) => ResolveUniverseRegionByRegionIdJob::dispatchSync($region_id));
    }

    private function handleItems()
    {
        if (!Arr::has($this->validatedData, 'items')) {
            // return early if systems is not part of the validated data, f.e. when updating regions
            return;
        }

        collect(data_get($this->validatedData, 'items', []))
            ->groupBy('watchable_type')->pipe(function (Collection $collection) {
                foreach ([Type::class, Group::class, Category::class] as $key) {
                    if (!$collection->has($key)) {
                        $collection = $collection->mergeRecursive([$key => []]);
                    }
                }

                return $collection;
            })
            ->each(fn ($items, $category) => match ($category) {
                Type::class     => $this->enlistment->types()->sync(data_get($items, '*.watchable_id')),
                Group::class    => $this->enlistment->groups()->sync(data_get($items, '*.watchable_id')),
                Category::class => $this->enlistment->categories()->sync(data_get($items, '*.watchable_id')),
            });
    }
}
