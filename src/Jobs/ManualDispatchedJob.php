<?php


namespace Seatplus\Web\Jobs;

use Illuminate\Support\Facades\Bus;

class ManualDispatchedJob
{

    private array $jobs;

    private string $name = 'manual job';

    /**
     * @param array $jobs
     * @return ManualDispatchedJob
     */
    public function setJobs(array $jobs): ManualDispatchedJob
    {
        $this->jobs = $jobs;
        return $this;
    }

    public function handle()
    {
        $success_message = sprintf('Manual update batch of %s processed!', $this->name);

        $batch = Bus::batch($this->jobs)
            ->then(fn() => logger()->info($success_message))
            ->name($this->name)
            ->onQueue('high')
            ->dispatch();

        return $batch->id;
    }

    /**
     * @param string $name
     * @return ManualDispatchedJob
     */
    public function setName(string $name): ManualDispatchedJob
    {
        $this->name = $name;
        return $this;
    }


}
