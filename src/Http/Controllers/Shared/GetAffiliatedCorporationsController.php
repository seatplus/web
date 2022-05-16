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

use Illuminate\Database\Eloquent\Builder;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Traits\HasAffiliated;
use Seatplus\Eveapi\Models\Character\CharacterAffiliation;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\CorporationInfoRessource;

class GetAffiliatedCorporationsController extends Controller
{

    use HasAffiliated;

    public function __invoke(string $permission, string $corporation_roles = '')
    {

        $corporation_roles = explode('|', $corporation_roles);

        $search_param = request()->get('search');

        $owned_corporations = CorporationInfo::query()
            ->joinSub(CharacterUser::query()
                ->whereHas(
                    'character.roles',
                    function (Builder $query) use ($corporation_roles) {
                        $query->whereJsonContains('roles', 'Director');


                        foreach ($corporation_roles as $role) {
                            $query->orWhereJsonContains('roles', $role);
                        }
                    }
                )
                ->addSelect([
                        'corporation_id' => CharacterAffiliation::query()
                            ->select('corporation_id')
                            ->whereColumn('character_users.character_id', 'character_id')
                    ]
                )
                ->where('user_id', auth()->user()->getAuthIdentifier()),
                'owned',
                'owned.corporation_id',
                'corporation_infos.corporation_id'
            )
            ->select('corporation_infos.*')
            ->when($search_param, fn($query) => $query
                ->where('name', 'like', "%${search_param}%")
            );

        $affiliatables = $this->scopeAffiliatedCorporations(CorporationInfo::query(), 'corporation_id', $permission, $corporation_roles)
            ->when($search_param, fn($query) => $query->where('name', 'like', "%${search_param}%"));

        $query = $owned_corporations
            ->union($affiliatables);

        return CorporationInfoRessource::collection(
            $query
                ->with('alliance')
                ->has($permission)
                ->paginate()
        );
    }
}
