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
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Http\Actions\Corporation\Recruitment\WatchlistArrayAction;
use Seatplus\Web\Http\Resources\CorporationComplianceResource;

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
            'canReview'    => auth()->user()->can('member compliance: review user'),
        ]);
    }

    public function getCorporationCompliance(int $corporation_id, string $type)
    {
        $isCharacterType = $type === 'default';
        $search = request()->get('search');

        $users = User::query()
            ->when($search, fn (Builder $query) => $query->whereHas('characters', fn (Builder $query) => $query->where('character_infos.name', 'like', "%${search}%")))
            ->whereHas('characters.corporation', fn (Builder $query) => $query
                ->where('corporation_infos.corporation_id', $corporation_id))
            ->with([
                'characters' => fn ($query) => $query->select('character_infos.character_id', 'character_infos.name')
                    ->when($isCharacterType, fn ($query) => $query->whereHas('corporation', fn (Builder $query) => $query->where('corporation_infos.corporation_id', $corporation_id))),
                'main_character',
                'characters.corporation.ssoScopes',
                'characters.alliance.ssoScopes',
                'characters.application.corporation.ssoScopes',
                'characters.application.corporation.alliance.ssoScopes',
                'characters.refresh_token',
                'application.corporation.ssoScopes',
                'application.corporation.alliance.ssoScopes',
            ]);

        return CorporationComplianceResource::collection($users->paginate());
    }

    public function reviewUser(int $corporation_id, User $user, WatchlistArrayAction $action)
    {
        $type = SsoScopes::where('morphable_id', $corporation_id)->limit(1)->pluck('type')->first();
        $isCharacterType = $type === 'default';

        $member = $user
            ->loadMissing([
                'characters' => fn ($query) => $query->select('character_infos.character_id', 'character_infos.name')
                    ->when($isCharacterType, fn ($query) => $query->whereHas('corporation', fn (Builder $query) => $query->where('corporation_infos.corporation_id', $corporation_id))),
                'main_character',
            ]);

        return inertia('Corporation/MemberCompliance/ReviewUser', [
            'member'            => $member,
            'targetCorporation' => CorporationInfo::find($corporation_id),
            'watchlist'         => $action->execute($corporation_id),
        ]);
    }
}
