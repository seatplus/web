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

namespace Seatplus\Web\Http\Controllers\Corporation\MemberCompliance;

use Illuminate\Database\Eloquent\Builder;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Http\Resources\CharacterComplianceResource;
use Seatplus\Web\Http\Resources\UserComplianceResource;

class MemberComplianceController
{
    public function index()
    {
        $affiliated_ids = getAffiliatedIdsByPermission('view member compliance', 'director');

        $affiliated_corporations = CorporationInfo::whereIn('corporation_id', $affiliated_ids)
            ->has('ssoScopes')
            ->orHas('alliance.ssoScopes')
            ->select('name', 'corporation_id')
            ->addSelect(['type' => SsoScopes::whereColumn('morphable_id', 'corporation_id')->limit(1)->select('type')])
            ->get();

        return inertia('Corporation/MemberCompliance/MemberCompliance', [
            'corporations' => $affiliated_corporations,
        ]);
    }

    public function getCharacterCompliance(int $corporation_id)
    {
        $members = CharacterInfo::whereHas('corporation', fn (Builder $query) => $query
            ->where('corporation_infos.corporation_id', $corporation_id)
            ->whereHas('ssoScopes', fn (Builder $query) => $query->whereType('default'))
        );

        return CharacterComplianceResource::collection($members->paginate());
    }

    public function getUserCompliance(int $corporation_id)
    {
        $users = $this->getUsersBuilder($corporation_id);

        return UserComplianceResource::collection($users->paginate());
    }

    public function getMissingCharacters(int $corporation_id)
    {
        $users = $this->getUsersBuilder($corporation_id);

        $known_ids = $users->cursor()
            ->map(fn ($user) => $user->characters)
            ->map(fn ($character) => $character->pluck('character_id'))
            ->flatten()
            ->unique()
            ->all();

        $members = CharacterInfo::whereHas('corporation', fn (Builder $query) => $query
            ->where('corporation_infos.corporation_id', $corporation_id)
            ->whereHas('ssoScopes', fn (Builder $query) => $query->whereType('user')))
            ->whereNotIn('character_id', $known_ids);

        return CharacterComplianceResource::collection($members->paginate());
    }

    private function getUsersBuilder(int $corporation_id): Builder
    {
        return User::whereHas('characters.corporation', fn (Builder $query) => $query
            ->where('corporation_infos.corporation_id', $corporation_id)
            ->whereHas('ssoScopes', fn (Builder $query) => $query->whereType('user'))
        );
    }
}
