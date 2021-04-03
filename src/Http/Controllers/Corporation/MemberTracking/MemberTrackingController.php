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
use Seatplus\Eveapi\Jobs\Hydrate\Corporation\CorporationMemberTrackingHydrateBatch;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\MemberTrackingResource;

class MemberTrackingController extends Controller
{
    public function index()
    {
        return Inertia::render('Corporation/MemberTracking/MemberTracking', [
            'dispatch_transfer_object' => $this->buildDispatchTransferObject(),
            'corporations' => $this->getAffiliatedCorporations(),
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

    private function buildDispatchTransferObject(): object
    {
        return (object) [
            'manual_job' => array_search(CorporationMemberTrackingHydrateBatch::class, config('web.jobs')),
            'permission' => $this->getPermission(),
            'required_scopes' => [...config('eveapi.scopes.corporation.membertracking'), 'esi-characters.read_corporation_roles.v1'],
            'required_corporation_role' => 'Director',
        ];
    }

    private function getPermission(): string
    {
        return config('eveapi.permissions.' . CorporationMemberTracking::class);
    }

    private function getAffiliatedCorporations()
    {
        return CorporationInfo::whereIn('corporation_id', getAffiliatedIdsByPermission($this->getPermission()))
            ->with('alliance','ssoScopes','alliance.ssoScopes')
            ->has('members')
            ->get()
            ->map(function($corporation) {

                $sso_scopes = collect([
                    'corporation_scopes' => $corporation?->ssoScopes?->selected_scopes,
                    'alliance_scopes' => $corporation?->alliance?->ssoScopes?->selected_scopes,
                ])->flatten(1)->filter();

                return [
                    'corporation_id' => $corporation->corporation_id,
                    'name' => $corporation->name,
                    'required_scopes' => $sso_scopes->unique()->toArray()
                ];
            });
    }
}
