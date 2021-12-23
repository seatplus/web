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

use Seatplus\Web\Http\Actions\Corporation\Recruitment\UpdateWatchlistAction;
use Seatplus\Web\Http\Actions\Corporation\Recruitment\WatchedArrayAction;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\CreateOpenRecruitmentRequest;
use Seatplus\Web\Http\Controllers\Request\UpdateWatchlistRequest;
use Seatplus\Web\Models\Recruitment\Enlistment;

class EnlistmentsController extends Controller
{
    public function create(CreateOpenRecruitmentRequest $request)
    {
        $enlistment = Enlistment::updateOrCreate(
            ['corporation_id' => $request->get('corporation_id')],
            ['type' => $request->get('type')]
        );

        return redirect()->back()->with('success', 'enlistment created');
    }

    public function delete(int $corporation_id)
    {
        Enlistment::where('corporation_id', $corporation_id)->delete();

        return back()->with('success', 'corporation is closed for recruitment');
    }

    public function watchlist(int $corporation_id, WatchedArrayAction $action)
    {
        return inertia('Corporation/Recruitment/Watchlist/Index', [
            'activeSidebarElement' => 'corporation.recruitment',
            'corporationId' => $corporation_id,
            'watched' => $action->execute($corporation_id),
        ]);
    }

    public function updateWatchlist(int $corporation_id, UpdateWatchlistRequest $request, UpdateWatchlistAction $action)
    {
        $action->execute($corporation_id, $request->validated());

        return back()->with('success', 'updated');
    }
}
