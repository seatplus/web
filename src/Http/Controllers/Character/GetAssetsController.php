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

namespace Seatplus\Web\Http\Controllers\Character;

use Illuminate\Http\Request;
use Seatplus\Eveapi\Http\Resources\CharacterAsset as CharacterAssetResource;
use Seatplus\Eveapi\Models\Assets\CharacterAsset;

class GetAssetsController
{
    public function __invoke(Request $request)
    {
        $character_ids = $request->query('character_ids');

        $query = CharacterAsset::with('location', 'location.locatable', 'owner', 'type', 'type.group', 'content')
            ->when($character_ids, function ($query, $character_ids) {
                $affiliated_ids = getAffiliatedIdsByClass(CharacterAsset::class);

                return $query->entityFilter(collect($character_ids)->map(fn ($character_id) => intval($character_id))->intersect($affiliated_ids)->toArray());
            }, function ($query) {
                return $query->entityFilter(auth()->user()->characters->pluck('character_id')->toArray());
            })
            ->whereIn('location_flag', ['Hangar', 'AssetSafety', 'Deliveries'])
            ->orderBy('location_id', 'asc');

        if ($request->has('region')) {
            $query = $query->inRegion($request->query('region'));
        }

        if ($request->has('search')) {
            $query = $query->search($request->query('search'));
        }

        return CharacterAssetResource::collection(
            $query->paginate()
        );
    }
}
