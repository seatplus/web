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

namespace Seatplus\Web\Services\DispatchUpdates;

use Laravel\Horizon\Contracts\JobRepository;
use Seatplus\Eveapi\Jobs\ManualDispatchableJobInterface;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Services\FindCorporationRefreshToken;

class BuildDispatchableCorporationJobs
{
    private ManualDispatchableJobInterface $job;

    private array $affiliated_ids;

    private string $dispatchable_job_name;

    /**
     * @var \Illuminate\Contracts\Foundation\Application|mixed
     */
    private $jobs_repository;

    public function __invoke(ManualDispatchableJobInterface $job, string $permission)
    {
        $this->job = $job;
        $this->affiliated_ids = getAffiliatedIdsByPermission($permission, $this->job->getRequiredEveCorporationRole());
        $this->dispatchable_job_name = array_search(get_class($job), config('eveapi.jobs'));
        $this->jobs_repository = resolve(JobRepository::class);

        //dd($this->corporations());

        return [
            'job_name' => $this->dispatchable_job_name,
            'characters' => [],
            'corporations' => [...$this->corporations()],
        ];
    }

    private function corporations(): array
    {
        if (! $this->job->getRequiredEveCorporationRole()) {
            return [];
        }

        $find_corporation_refresh_token = new FindCorporationRefreshToken;

        return CorporationInfo::whereIn('corporation_id', $this->affiliated_ids)
            ->cursor()
            ->filter(fn ($corporation) => $find_corporation_refresh_token($corporation->corporation_id, $this->job->getRequiredScope(), $this->job->getRequiredEveCorporationRole()))
            ->map(function ($corporation) {
                $cache_key = sprintf('%s:%s', $this->dispatchable_job_name, $corporation->corporation_id);

                return [
                    'corporation_id' => $corporation->corporation_id,
                    'name' => $corporation->name,
                    'job' => cache($cache_key) ? $this->jobs_repository->getJobs([cache($cache_key)])->first() : null,
                ];
            })
            ->toArray();
    }
}
