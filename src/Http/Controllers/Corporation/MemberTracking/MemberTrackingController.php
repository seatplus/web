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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\MemberTrackingResource;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;
use Seatplus\Web\Services\Controller\DispatchTransferObject;

class MemberTrackingController extends Controller
{
    public function index(): Response
    {
        $dispatchTransferObject = CreateDispatchTransferObject::new()
            ->setIsCharacter(false)
            ->create(CorporationMemberTracking::class);

        return Inertia::render('Corporation/MemberTracking/MemberTracking', [
            'dispatchTransferObject' => $dispatchTransferObject,
            'corporations' => $this->getAffiliatedCorporations($dispatchTransferObject),
        ]);
    }

    public function getMemberTracking(int $corporation_id): AnonymousResourceCollection
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

    private function getAffiliatedCorporations(DispatchTransferObject $dispatchTransferObject): Collection
    {

        $affiliatedIds = $this->getAffiliatedIds($dispatchTransferObject);

        return CorporationInfo::query()
            ->whereIn('corporation_id', $affiliatedIds)
            ->with('alliance')
            ->where(
                fn (Builder $query) => $query
                    ->has('alliance.ssoScopes')
                    ->orHas('ssoScopes')
            )
            ->has('members')
            ->addSelect([
                'corporation_scopes' => SsoScopes::select('selected_scopes')->whereColumn('morphable_id', 'corporation_infos.corporation_id')->limit(1),
                'alliance_scopes' => SsoScopes::select('selected_scopes')->whereColumn('morphable_id', 'corporation_infos.alliance_id')->limit(1),
            ])
            ->get()
            ->map(function (CorporationInfo $corporation) {
                $required_scopes = collect([
                    json_decode((string) $corporation->getAttribute('corporation_scopes'), true),
                    json_decode((string) $corporation->getAttribute('alliance_scopes'), true),
                ])->flatten()->filter()->unique()->toArray();

                $corporation->setAttribute('required_scopes', $required_scopes);

                return $corporation;
            });
    }
}
