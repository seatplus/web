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
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\CharacterInfoRessource;
use Seatplus\Web\Services\GetAffiliatedIds;
use Seatplus\Web\Services\GetRecruitIdsService;

class GetAffiliatedCharactersController extends Controller
{
    public function __construct(
        Request $request,
        GetAffiliatedIds $getAffiliatedIds,
    ) {
        parent::__construct($request, $getAffiliatedIds);
    }

    public function __invoke(string $permission): AnonymousResourceCollection
    {
        $search_param = request()->get('search');

        $affiliatedIds = $this->getAffiliatedIds->get($permission);
        $user_character_ids = auth()->user()->characters->pluck('character_id')->toArray();
        $recruit_ids = GetRecruitIdsService::get();

        $owned_characters = $this->getCharacterInfoQuery($affiliatedIds, $search_param);
        $recruits = $this->getCharacterInfoQuery($recruit_ids, $search_param);
        $affiliatables = $this->getCharacterInfoQuery($user_character_ids, $search_param);

        $query = $owned_characters
            ->union($recruits)
            ->union($affiliatables)
            ->distinct()
            ->with(['corporation', 'alliance'])
            ->has($permission)
            ->paginate();

        return CharacterInfoRessource::collection($query);
    }

    private function getCharacterInfoQuery(array $ids, ?string $search_param = null): Builder
    {
        return CharacterInfo::query()
            ->whereIn('character_id', $ids)
            ->when($search_param, fn (mixed $query) => $query->where('character_infos.name', 'like', "%{$search_param}%"))
            ->orderBy('character_infos.name');
    }
}
