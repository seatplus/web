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
use Inertia\Inertia;
use Seatplus\Eveapi\Models\Assets\Asset as EveApiAsset;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\AssetResource;
use Seatplus\Web\Models\Asset\Asset as WebAssetAlias;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Traits\HasWatchlist;

class AssetsController extends Controller
{
    use HasWatchlist;

    public function index()
    {
        $dispatchTransferObject = $this->getDispatchTransferObject();

        return Inertia::render('Character/Assets', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'characterIds' => $this->getCharacterIds($dispatchTransferObject, 'assets'),
        ]);
    }

    public function getLocations(Request $request)
    {
        $character_ids = $request->get('character_ids');

        abort_unless($character_ids, 404);

        $query = WebAssetAlias::query()
            ->whereIn('assetable_id', $character_ids)
            ->where('assetable_type', CharacterInfo::class)
            ->when($request->has('search'), fn ($query) => $query->search($request->get('search')))
            ->handleWatchlist($request)
            ->withRecursiveContent()
            ->with('location')
            ->select('location_id', 'assetable_id')
            ->whereIn('location_flag', ['Hangar', 'AssetSafety', 'Deliveries'])
            ->groupBy('location_id', 'assetable_id')
            ->orderBy('location_id', 'asc');

        if($request->has('withUnknownLocations')) {

            $query = WebAssetAlias::query()
                ->whereIn('assetable_id', $character_ids)
                ->where('assetable_type', CharacterInfo::class)
                ->withUnknownLocations()
                ->with('location')
                ->select('location_id', 'assetable_id')
                ->whereIn('location_flag', ['Hangar', 'AssetSafety', 'Deliveries'])
                ->groupBy('location_id', 'assetable_id')
                ->orderBy('location_id', 'asc');
        }

        return AssetResource::collection(
            $query->paginate(5)
        );
    }

    public function loadLocation(int $location_id, Request $request)
    {
        $character_ids = $request->get('character_ids');

        abort_unless($character_ids, 404);

        $query = WebAssetAlias::query()
            ->whereIn('assetable_id', $character_ids)
            ->where('assetable_type', CharacterInfo::class)
            ->when($request->has('search'), fn ($query) => $query->search($request->get('search')))
            ->handleWatchlist($request)
            ->withRecursiveContent()
            ->where('location_id', $location_id)
            ->with(['assetable', 'type', 'type.group', 'content', 'location']);

        return AssetResource::collection(
            $query->paginate()
        );
    }

    public function item(int $character_id, int $item_id)
    {
        $query = EveApiAsset::with([
            'location', 'type', 'type.group', 'container',
            'content' => ['content', 'type', 'type.group', 'assetable'],
        ])
            ->where('assetable_id', $character_id)
            ->where('assetable_type', CharacterInfo::class)
            ->where('item_id', $item_id);

        $item = AssetResource::collection($query->get());

        return Inertia::render('Character/ItemDetails', [
            'item' => $item,
        ]);
    }

    private function getDispatchTransferObject()
    {
        return CreateDispatchTransferObject::new()->create(EveApiAsset::class);
    }
}
