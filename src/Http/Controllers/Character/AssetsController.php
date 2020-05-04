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
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Seatplus\Eveapi\Http\Resources\CharacterAsset as CharacterAssetResource;
use Seatplus\Eveapi\Models\Assets\CharacterAsset;
use Seatplus\Eveapi\Models\Universe\Region;
use Seatplus\Web\Http\Controllers\Controller;

class AssetsController extends Controller
{
    public function index(Request $request)
    {

        // TODO create validator
        $validatedData = $request->validate([
            'character_id' => [Rule::in(auth()->user()->getAffiliatedCharacterIdsByPermission(CharacterAsset::class)), 'numeric'],
            'region_id' => ['exists:universe_regions,region_id', 'numeric'],
            'search_param' => 'string',
        ]);

        $character_id = $request->has('character_id') ? $validatedData['character_id'] : null;

        $query = CharacterAsset::Affiliated($character_id)
            ->with('location','location.locatable', 'owner', 'type', 'type.group', 'content')
            ->whereIn('location_flag', ['Hangar', 'AssetSafety', 'Deliveries'])
            ->orderBy('location_id', 'desc');

        if($request->has('region_id'))
            $query = $query->inRegion($validatedData['region_id']);

        if($request->has('search_param'))
            $query = $query->search($validatedData['search_param']);

        $filters = function () {

            $affiliated_characters = getAffiliatedCharacters(CharacterAsset::class);
            $owned_character_ids = auth()->user()->characters->pluck('character_id');

            return [
                'regions' => Region::all(),
                //TODO: create service
                'affiliated_characters' => $affiliated_characters->filter(function ($character_info) use ($owned_character_ids) {
                    return ! in_array($character_info->character_id, $owned_character_ids->toArray());
                }),
                'owned_characters' => $affiliated_characters->filter(function ($character_info) use ($owned_character_ids) {
                    return in_array($character_info->character_id, $owned_character_ids->toArray());
                }),
            ];
        };

        $assets = CharacterAssetResource::collection(
            $query->paginate()
        );

        return Inertia::render('Character/Assets', [
            'filters' => $filters,
            'assets' => $assets,
        ]);
    }

    public function details(int $item_id)
    {
        $query = CharacterAsset::Affiliated()
            ->with('type', 'type.group', 'content', 'content.type', 'content.type.group')
            ->where('item_id', $item_id);

        $assets = CharacterAssetResource::collection(
            $query->paginate()
        );

        return Inertia::render('Character/ItemDetails', [
            'assets' => $assets,
        ]);
    }
}
