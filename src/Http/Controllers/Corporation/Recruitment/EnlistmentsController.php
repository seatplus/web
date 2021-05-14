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

use Illuminate\Http\Request;
use Seatplus\Eveapi\Jobs\Universe\ResolveUniverseRegionByRegionIdJob;
use Seatplus\Eveapi\Jobs\Universe\ResolveUniverseSystemBySystemIdJob;
use Seatplus\Eveapi\Models\Universe\Region;
use Seatplus\Eveapi\Models\Universe\System;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\CreateOpenRecruitmentRequest;
use Seatplus\Web\Models\Recruitment\Enlistment;

class EnlistmentsController extends Controller
{
    public function index()
    {
        return Enlistment::with('corporation', 'corporation.alliance')->get()->toJson();
    }

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

    public function watchlist(int $corporation_id)
    {
        $enlistment = Enlistment::with('systems', 'regions')->find($corporation_id);

        return inertia('Corporation/Recruitment/Watchlist/Index', [
            'activeSidebarElement' => route('corporation.recruitment'),
            'corporation_id' => $corporation_id,
            'watched_systems' => $enlistment->systems->map(function ($system) {
                $system->id = $system->system_id;

                return $system;
            }) ?? [],
            'watched_regions' => $enlistment->regions->map(function ($region) {
                $region->id = $region->region_id;

                return $region;
            }) ?? [],
        ]);
    }

    public function updateWatchlist(int $corporation_id, Request $request)
    {
        $validated_data = $request->validate([
            'systems' => ['array'],
            'regions' => ['array'],
        ]);

        $system_ids = data_get($validated_data, 'systems.*.id', []);
        $region_ids = data_get($validated_data, 'regions.*.id', []);

        $enlistment = Enlistment::find($corporation_id);

        $enlistment->systems()->sync($system_ids);

        collect($system_ids)
            ->diff(System::whereIn('system_id', $system_ids)->select('system_id')->pluck('system_id'))
            ->each( fn($system_id) => ResolveUniverseSystemBySystemIdJob::dispatchSync($system_id));

        $enlistment->regions()->sync($region_ids);

        collect($region_ids)
            ->diff(Region::whereIn('region_id', $region_ids)->select('region_id')->pluck('region_id'))
            ->each( fn($region_id) => ResolveUniverseRegionByRegionIdJob::dispatchSync($region_id));

        return back()->with('success', 'updated');
    }
}
