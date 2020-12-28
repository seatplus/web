<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
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
use Seatplus\Eveapi\Jobs\Corporation\CorporationMemberTrackingJob;
use Seatplus\Eveapi\Jobs\Hydrate\Corporation\CorporationMemberTrackingHydrateBatch;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\DispatchUpdates\BuildDispatchableCorporationJobs;

class IndexMemberTrackingController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Corporation/MemberTracking', [
            'required_scopes' => config('eveapi.scopes.character.contacts'),
            'dispatch_transfer_object' => $this->buildDispatchTransferObject(),
            'member_tracking' => CorporationMemberTracking::Affiliated()
                ->with('corporation.ssoScopes', 'corporation.alliance.ssoScopes', 'character.refresh_token', 'location.locatable', 'ship')
                ->get()
                ->groupBy('corporation_id')
                ->map(function ($members) {
                    $corporation = $members->first()->corporation;
                    $sso_scopes = collect([
                        'corporation_scopes' => $corporation->ssoScopes->selected_scopes ?? [],
                        'alliance_scopes' => $corporation->alliance->ssoScopes->selected_scopes ?? [],
                    ])->flatten(1)->filter();

                    return [
                        'corporation' => $corporation,
                        'members' => $members->map(function ($member) use ($sso_scopes) {
                            $member->missing_sso_scopes = $sso_scopes->diff($member->character->refresh_token->scopes ?? []);

                            return $member;
                        }),
                    ];
                }),
        ]);
    }

    //TODO:
    private function buildDispatchTransferObject(): object
    {
        return (object) [
            'manual_job' => array_search(CorporationMemberTrackingHydrateBatch::class, config('web.jobs')),
            'permission' => config('eveapi.permissions.' . CorporationMemberTracking::class),
            'required_scopes' => config('eveapi.scopes.corporation.membertracking'),
            'required_corporation_role' => 'Director',
        ];
    }

    private function getDispatchableJobs()
    {
        $dispatchableJobBuilder = new BuildDispatchableCorporationJobs;
        $job = new CorporationMemberTrackingJob;

        return $dispatchableJobBuilder($job, config('eveapi.permissions.' . CorporationMemberTracking::class));
    }
}
