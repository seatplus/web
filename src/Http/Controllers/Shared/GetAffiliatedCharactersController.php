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
use Illuminate\Database\Query\JoinClause;
use Seatplus\Auth\Traits\HasAffiliated;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\CharacterInfoRessource;
use Seatplus\Web\Services\GetRecruitIdsService;

class GetAffiliatedCharactersController extends Controller
{
    use HasAffiliated;

    public function __invoke(string $permission)
    {
        $search_param = request()->get('search');

        $owned_characters = CharacterInfo::query()
            ->join(
                'character_users',
                fn (JoinClause $join) => $join
                    ->on('character_infos.character_id', '=', 'character_users.character_id')
                    ->where('character_users.user_id', auth()->user()->getAuthIdentifier())
            )
            ->whereNotNull('character_users.character_id')
            ->when(
                $search_param,
                fn ($query) => $query
                ->where('name', 'like', "%${search_param}%")
            )
            ->select('character_infos.*');

        $recruits = CharacterInfo::query()
            ->whereIn('character_id', GetRecruitIdsService::get())
            ->when($search_param, fn ($query) => $query->where('name', 'like', "%${search_param}%"));

        $affiliatables = $this->scopeAffiliatedCharacters(CharacterInfo::query(), 'character_id', $permission)
            ->when($search_param, fn ($query) => $query->where('name', 'like', "%${search_param}%"));

        $query = $owned_characters
            ->union($recruits)
            ->union($affiliatables);

        /*request()->whenHas('search', fn(string $search_query) => $query
            ->where('name', 'like', "%${search_query}%")
        );*/

        $query->dump();

        $query = $query
            ->with('corporation', 'alliance')
            ->has($permission)
            ->paginate();

        return CharacterInfoRessource::collection($query);



        $ids = collect([...getAffiliatedIdsByPermission($permission), ...GetRecruitIdsService::get()])
            ->unique();

        $search_query = request()->get('search');

        $query = CharacterInfo::whereIn('character_id', $ids)
            ->with('corporation', 'alliance')
            ->when($search_query, fn (Builder $query) => $query->where('name', 'like', "%${search_query}%"))
            ->has($permission);

        return CharacterInfoRessource::collection($query->paginate());
    }
}
