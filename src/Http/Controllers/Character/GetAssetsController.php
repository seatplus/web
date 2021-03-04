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

namespace Seatplus\Web\Http\Controllers\Character;

use Illuminate\Http\Request;
use Seatplus\Eveapi\Models\Assets\Asset as EveApiAsset;
use Seatplus\Web\Http\Resources\AssetResource;
use Seatplus\Web\Models\Asset\Asset;
use Seatplus\Web\Services\GetRecruitIdsService;

class GetAssetsController
{
    public function __invoke(Request $request)
    {
        $query = Asset::with('location', 'location.locatable', 'assetable', 'type', 'type.group', 'content')
            ->affiliated([...getAffiliatedIdsByClass(EveApiAsset::class), ...GetRecruitIdsService::get()], request()->query('character_ids'))
            ->whereIn('location_flag', ['Hangar', 'AssetSafety', 'Deliveries'])
            ->orderBy('location_id', 'asc');

        if ($request->has('regions')) {
            $query = $query->inRegion($request->query('regions'));
        }

        if ($request->has('systems')) {
            $query = $query->inSystems($request->query('systems'));
        }

        if ($request->has('search')) {
            $query = $query->search($request->query('search'));
        }

        if ($request->has('withUnknownLocations')) {
            $query = $query->withUnknownLocations();
        }

        return AssetResource::collection(
            $query->paginate()
        );
    }
}
