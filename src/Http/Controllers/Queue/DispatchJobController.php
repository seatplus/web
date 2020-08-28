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

namespace Seatplus\Web\Http\Controllers\Queue;

use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Eveapi\Services\DispatchIndividualUpdate;
use Seatplus\Eveapi\Services\FindCorporationRefreshToken;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\DispatchIndividualJob;

class DispatchJobController extends Controller
{
    public function __invoke(DispatchIndividualJob $job)
    {

        $cache_key = sprintf('%s:%s', $job['job'], $job['character_id'] ?? $job->get('corporation_id'));

        if (cache($cache_key)) {
            return redirect()->back()->with('error', 'job was already queued');
        }

        $job_id = (new DispatchIndividualUpdate($this->getRefreshToken($job)))->execute($job['job']);

        cache([$cache_key => $job_id], now()->addHour());

        return redirect()->back()->with('success', 'job queued');
    }

    private function getRefreshToken(DispatchIndividualJob $job)
    {
        if($job->get('character_id'))
            return RefreshToken::find($job->get('character_id'));

        $dispatchable_job_class = config('eveapi.jobs')[$job->get('job')];

        $dispatchable_job = new $dispatchable_job_class;

        return (new FindCorporationRefreshToken)($job->get('corporation_id'), $dispatchable_job->getRequiredScope(), $dispatchable_job->getRequiredEveCorporationRole());
    }
}
