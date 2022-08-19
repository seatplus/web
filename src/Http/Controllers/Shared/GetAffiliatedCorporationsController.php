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

namespace Seatplus\Web\Http\Controllers\Shared;

use Seatplus\Auth\Services\Affiliations\GetOwnedAffiliatedIdsService;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\CorporationInfoRessource;

class GetAffiliatedCorporationsController extends Controller
{
    public function __invoke(string $permission, string $corporation_roles = '')
    {
        $affiliationsDto = new AffiliationsDto(
            user: auth()->user(),
            permissions: [$permission],
            corporation_roles: explode(',', $corporation_roles)
        );

        $search_param = request()->get('search');

        $owned_affiliations = GetOwnedAffiliatedIdsService::make($affiliationsDto)->getQuery();

        $owned_corporations = CorporationInfo::query()
            ->joinSub(
                $owned_affiliations,
                'owned',
                'owned.affiliated_id',
                '=',
                'corporation_infos.corporation_id'
            )
            ->select('corporation_infos.*')
            ->when($search_param, fn ($query) => $query->where('name', 'like', "%${search_param}%"));

        $affiliatables = CorporationInfo::query()
            ->whereAffiliatedCorporations($affiliationsDto)
            // Remove Doomheim corporation
            ->where('corporation_infos.corporation_id', '<>', 1_000_001)
            ->select('corporation_infos.*')
            ->has($permission)
            ->when($search_param, fn ($query) => $query->where('name', 'like', "%${search_param}%"));

        $query = $owned_corporations
            ->union($affiliatables);

        return CorporationInfoRessource::collection(
            $query
                ->with('alliance')
                ->paginate()
        );
    }
}
