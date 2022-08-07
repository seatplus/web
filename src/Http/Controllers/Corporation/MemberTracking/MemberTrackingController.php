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

namespace Seatplus\Web\Http\Controllers\Corporation\MemberTracking;

use Inertia\Inertia;
use Seatplus\Auth\Services\Affiliations\GetOwnedAffiliatedIdsService;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\MemberTrackingResource;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;

class MemberTrackingController extends Controller
{
    public function index()
    {
        $dispatchTransferObject = CreateDispatchTransferObject::new()
            ->setIsCharacter(false)
            ->create(CorporationMemberTracking::class);

        return Inertia::render('Corporation/MemberTracking/MemberTracking', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'corporations' => $this->getAffiliatedCorporations($dispatchTransferObject),
        ]);
    }

    public function getMemberTracking(int $corporation_id)
    {
        $corporation = CorporationInfo::find($corporation_id);
        $sso_scopes = collect([
            'corporation_scopes' => $corporation?->ssoScopes?->selected_scopes,
            'alliance_scopes' => $corporation?->alliance?->ssoScopes?->selected_scopes,
        ])->flatten(1)->filter();

        $query = CorporationMemberTracking::where('corporation_id', $corporation_id)
            ->with('character.refresh_token', 'location.locatable', 'ship');

        return MemberTrackingResource::collection($query->paginate())->additional(['required_scopes' => $sso_scopes]);
    }

    private function getAffiliatedCorporations(object $dispatchTransferObject)
    {
        $affiliations_dto = new AffiliationsDto(
            permissions: [data_get($dispatchTransferObject, 'permission')],
            user: auth()->user(),
            corporation_roles: data_get($dispatchTransferObject, 'required_corporation_role')
        );

        $owned_corporations = GetOwnedAffiliatedIdsService::make($affiliations_dto)
            ->getQuery();

        return CorporationInfo::query()
            ->with('alliance')
            ->where(
                fn ($query) => $query
                ->has('alliance.ssoScopes')
                ->orHas('ssoScopes')
            )
            ->has('members')
            ->addSelect([
                'corporation_scopes' => SsoScopes::select('selected_scopes')->whereColumn('morphable_id', 'corporation_infos.corporation_id')->limit(1),
                'alliance_scopes' => SsoScopes::select('selected_scopes')->whereColumn('morphable_id', 'corporation_infos.alliance_id')->limit(1),
            ])
            ->whereAffiliatedCorporations($affiliations_dto)
            ->get()
            ->map(function ($corporation) {

                $corporation->required_scopes = collect([
                    json_decode($corporation->corporation_scopes, true),
                    json_decode($corporation->alliance_scopes,true)
                ])->flatten()->filter()->unique()->toArray();

                return $corporation;
            });
    }
}
