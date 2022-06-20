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

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Seatplus\Auth\Services\Affiliations\GetAffiliatedIdsService;
use Seatplus\Auth\Services\Affiliations\GetOwnedAffiliatedIdsService;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Assets\Asset as EveApiAsset;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\AssetResource;
use Seatplus\Web\Models\Asset\Asset as WebAssetAlias;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Services\GetRecruitIdsService;
use Seatplus\Web\Traits\HasWatchlist;

class AssetsController extends Controller
{
    use HasWatchlist;

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
            ->whereIn('location_flag', ['Hangar', 'AssetSafety', 'Deliveries'])
            ->select('location_id')
            ->groupBy('location_id')
            ->orderBy('location_id', 'asc');

        $query = $this->handleAffiliated($query);

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
            ->where('location_id', $location_id);

        $query = $this->handleAffiliated($query);

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

    private function handleAffiliated(Builder $query) : Builder
    {

        $affiliationsDto = new AffiliationsDto(
            user: auth()->user(),
            permissions: [config('eveapi.permissions.' . Asset::class)]
        );

        $affiliations = request()->hasAny(['character_ids', 'corporation_ids'])
            ? GetAffiliatedIdsService::make($affiliationsDto)->getQuery()
            : GetOwnedAffiliatedIdsService::make($affiliationsDto)->getQuery();

        request()->whenHas('character_ids', fn(array $character_ids) => $affiliations->whereIn('affiliated_id', $character_ids));
        request()->whenHas('corporation_ids', fn(array $corporation_ids) => $affiliations->whereIn('affiliated_id', $corporation_ids));

        $table = (new Asset)->getTable();

        return $query->joinSub(
            $affiliations,
            'affiliations',
            fn ($query) => $query
                ->on('affiliations.affiliated_id', '=', "$table.assetable_id")
                ->where("$table.assetable_type", CharacterInfo::class)
        );
    }
}
