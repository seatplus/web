<?php


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
        $this->dispatchable_job_name = array_search(get_class($job),config('eveapi.jobs'));
        $this->jobs_repository = resolve(JobRepository::class);

        //dd($this->corporations());

        return [
            'job_name' => $this->dispatchable_job_name,
            'characters' => [],
            'corporations' => [...$this->corporations()]
        ];
    }

    private function corporations(): array
    {
        if(!$this->job->getRequiredEveCorporationRole())
            return [];

        $find_corporation_refresh_token = new FindCorporationRefreshToken;

        return CorporationInfo::whereIn('corporation_id', $this->affiliated_ids)
            ->cursor()
            ->filter(fn($corporation) => $find_corporation_refresh_token($corporation->corporation_id, $this->job->getRequiredScope(), $this->job->getRequiredEveCorporationRole()))
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
