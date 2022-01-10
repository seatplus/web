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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Seatplus\Eveapi\Models\Assets\Asset as EveApiAsset;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\AssetResource;
use Seatplus\Web\Models\Asset\Asset as WebAssetAlias;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Services\GetRecruitIdsService;

class AssetsController extends Controller
{
    public function index()
    {
        return Inertia::render('Character/Assets', [
            'dispatchTransferObject' => CreateDispatchTransferObject::new()->create(EveApiAsset::class),
        ]);
    }

    public function getLocations(Request $request)
    {
        $query = WebAssetAlias::query()
            ->with('location')
            ->affiliated([...getAffiliatedIdsByClass(EveApiAsset::class), ...GetRecruitIdsService::get()], request()->query('character_ids'))
            ->whereIn('location_flag', ['Hangar', 'AssetSafety', 'Deliveries'])
            ->select('location_id')
            ->groupBy('location_id')
            ->orderBy('location_id', 'asc');

        $request->whenHas('search', fn ($term) => $query->search($term));

        $this->handleWatchlist($query, $request);

        if ($request->has('withUnknownLocations')) {
            $query = $query->withUnknownLocations();
        }

        return AssetResource::collection(
            $query->paginate(3)
        );
    }

    public function loadLocation(int $location_id, Request $request)
    {
        $query = EveApiAsset::with(['assetable', 'type', 'type.group', 'content'])
            ->affiliated([...getAffiliatedIdsByClass(EveApiAsset::class), ...GetRecruitIdsService::get()], request()->query('character_ids'))
            ->where('location_id', $location_id);

        $request->whenHas('search', fn ($term) => $query->search($term));

        $this->handleWatchlist($query, $request);

        return AssetResource::collection(
            $query->paginate()
        );
    }

    public function details(int $item_id)
    {
        $query = EveApiAsset::with('location', 'type', 'type.group', 'container', 'content', 'content.content', 'content.type', 'content.type.group')
            ->whereIn('assetable_id', [...getAffiliatedIdsByClass(EveApiAsset::class), ...GetRecruitIdsService::get()])
            ->where('item_id', $item_id);

        $item = AssetResource::collection($query->get());

        return Inertia::render('Character/ItemDetails', [
            'item' => $item,
        ]);
    }

    private function handleWatchlist(Builder|\Illuminate\Database\Query\Builder $query, Request $request)
    {
        $query->where(function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $request->whenHas('systems', fn ($system_ids) => $query->orWhere(fn ($query) => $query->inSystems($system_ids)));
                $request->whenHas('regions', fn ($region_ids) => $query->orWhere(fn ($query) => $query->inRegion($region_ids)));
            })
                ->orWhere(fn (Builder $query) => $query
                    ->when($request->hasAny(['types', 'groups', 'categories']), fn ($query) => $query->where(function ($query) use ($request) {
                        $request->whenHas('types', fn ($type_ids) => $query->orWhere(fn ($query) => $query->ofTypes($type_ids)));
                        $request->whenHas('groups', fn ($group_ids) => $query->orWhere(fn ($query) => $query->ofGroups($group_ids)));
                        $request->whenHas('categories', fn ($category_ids) => $query->orWhere(fn ($query) => $query->ofCategories($category_ids)));
                    }))
                );
        });
    }
}
