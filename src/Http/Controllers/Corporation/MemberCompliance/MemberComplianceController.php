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
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Response;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Http\Actions\Corporation\Recruitment\WatchlistArrayAction;
use Seatplus\Web\Http\Resources\CorporationComplianceResource;
use Seatplus\Web\Services\GetAffiliatedIds;
use Seatplus\Web\Support\Translations;

class MemberComplianceController
{
    public function index(): Response
    {
        /** @var User $user */
        $user = auth()->user();

        $affiliated_corporations = CorporationInfo::query()
            ->has('ssoScopes')
            ->orHas('alliance.ssoScopes')
            ->when(
                ! $user->can('superuser'),
                function (Builder $query) use ($user) {
                    $affiliated_ids = (new GetAffiliatedIds($user))->get(
                        permissions: 'view member compliance',
                        corporationRoles: 'director'
                    );

                    $query->whereIn('corporation_infos.corporation_id', $affiliated_ids);
                }
            )
            ->select('name', 'corporation_id')
            ->addSelect(['type' => SsoScopes::whereColumn('morphable_id', 'corporation_id')->limit(1)->select('type')])
            ->get();

        return inertia('Corporation/MemberCompliance/MemberCompliance', [
            'corporations' => $affiliated_corporations,
            'canReview' => auth()->user()->can('member compliance: review user'),
        ]);
    }

    public function getCorporationCompliance(int $corporation_id, string $type): AnonymousResourceCollection
    {
        $isCharacterType = $type === 'default';
        $search = request()->get('search');

        $users = User::query()
            ->when($search, fn (Builder $query) => $query->whereHas('characters', fn (Builder $query) => $query->where('character_infos.name', 'like', "%${search}%")))
            ->whereHas('characters.corporation', fn (Builder $query) => $query
                ->where('corporation_infos.corporation_id', $corporation_id))
            ->with([
                'characters' => fn (Relation $query) => $query->select('character_infos.character_id', 'character_infos.name')
                    ->when($isCharacterType, fn (Builder $query) => $query->whereHas('corporation', fn (Builder $query) => $query->where('corporation_infos.corporation_id', $corporation_id))),
                'mainCharacter',
                'characters.corporation.ssoScopes',
                'characters.alliance.ssoScopes',
                'characters.application.corporation.ssoScopes',
                'characters.application.corporation.alliance.ssoScopes',
                'characters.refreshToken',
                'application.corporation.ssoScopes',
                'application.corporation.alliance.ssoScopes',
            ]);

        // Fetched in full client-side (http.js getJson) with client-side renegade/loyalist
        // filtering, so return the whole list rather than paginating.
        return CorporationComplianceResource::collection($users->get());
    }

    public function reviewUser(int $corporation_id, User $user, WatchlistArrayAction $action): Response
    {
        $type = SsoScopes::where('morphable_id', $corporation_id)->limit(1)->value('type');
        $isCharacterType = $type === 'default';

        $member = $user
            ->loadMissing([
                'characters' => fn (Relation $query) => $query->select('character_infos.character_id', 'character_infos.name')
                    ->when($isCharacterType, fn (Builder $query) => $query->whereHas('corporation', fn (Builder $query) => $query->where('corporation_infos.corporation_id', $corporation_id))),
                'mainCharacter',
            ]);

        return inertia('Corporation/MemberCompliance/ReviewUser', [
            'member' => $member,
            'targetCorporation' => CorporationInfo::find($corporation_id),
            'watchlist' => $action->execute($corporation_id),
            'pageTranslations' => Translations::gather(['web::wallet_journal']),
        ]);
    }
}
