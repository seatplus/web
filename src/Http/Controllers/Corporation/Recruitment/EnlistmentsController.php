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

namespace Seatplus\Web\Http\Controllers\Corporation\Recruitment;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Http\Actions\Corporation\Recruitment\UpdateWatchlistAction;
use Seatplus\Web\Http\Actions\Corporation\Recruitment\WatchedArrayAction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\CreateOpenRecruitmentRequest;
use Seatplus\Web\Http\Controllers\Request\UpdateWatchlistRequest;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Services\DispatchCorporationOrAllianceInfoJob;

class EnlistmentsController extends Controller
{
    /**
     * Open (or update) a corporation's Job Posting.
     *
     * INTENTIONAL AUTHORIZATION RELAXATION: this action is gated on the plain
     * `can open or close corporations for recruitment` permission only — it is NOT scoped to the
     * user's affiliated corporations (the create route was deliberately removed from the
     * CheckAuthorization/CanUserService affiliation gate). A permission-holder may therefore open
     * ANY corporation for recruitment, including ones they are not affiliated with, so recruiters
     * can post for a corp before its data has ever been pulled. Reviewers: this is by design.
     *
     * Because any corp_id is accepted, the corporation may not yet exist locally; ensure its
     * CorporationInfo is (being) populated via the same public-ESI id-resolution path the SSO-scope
     * "applies to" picker uses, so the freshly-created posting card can render its logo/name.
     */
    public function create(CreateOpenRecruitmentRequest $request, DispatchCorporationOrAllianceInfoJob $dispatchCorporationInfo): RedirectResponse
    {
        $corporationId = (int) data_get($request->validated(), 'corporation_id');

        if (! CorporationInfo::query()->where('corporation_id', $corporationId)->exists()) {
            $dispatchCorporationInfo->handle(CorporationInfo::class, $corporationId);
        }

        $enlistment = Enlistment::query()->updateOrCreate(
            ['corporation_id' => $corporationId],
            [
                'type' => data_get($request->validated(), 'type'),
                'steps' => data_get($request->validated(), 'steps') ?? '',
            ]
        );

        return redirect()->back()->with('success', $enlistment->wasRecentlyCreated ? 'Job posting created' : 'Job posting updated');
    }

    public function delete(int $corporation_id): RedirectResponse
    {
        Enlistment::where('corporation_id', $corporation_id)->delete();

        return redirect()->action([GetRecruitmentIndexController::class])->with('success', 'Job posting closed');
    }

    public function edit(int $corporation_id, WatchedArrayAction $action): Response
    {
        return inertia('Corporation/Recruitment/Configuration/Index', [
            'activeSidebarElement' => 'corporation.recruitment',
            'corporationId' => $corporation_id,
            'enlistment' => Enlistment::find($corporation_id),
            'watched' => $action->execute($corporation_id),
        ]);
    }

    public function updateWatchlist(int $corporation_id, UpdateWatchlistRequest $request, UpdateWatchlistAction $action): RedirectResponse
    {
        $action->execute($corporation_id, $request->validated());

        return back()->with('success', 'updated');
    }
}
