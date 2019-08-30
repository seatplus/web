<?php

namespace Seatplus\Web\Http\Controllers\Queue;

use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\Contracts\WorkloadRepository;
use Seatplus\Web\Http\Controllers\Controller;

class QueueController extends Controller
{
    public function getStatus()
    {
        return [
            'queue_count' => collect(resolve(WorkloadRepository::class)->get())
                ->sum('length'),
            'error_count' => app(JobRepository::class)->countRecentlyFailed(),
            'status' => $this->currentStatus(),
        ];
    }

    /**
     * @return string
     */
    private function currentStatus()
    {
        if (! $masters = app(MasterSupervisorRepository::class)->all()) {
            return 'inactive';
        }

        return collect($masters)->contains(function ($master) {
            return $master->status === 'paused';
        }) ? 'paused' : 'running';
    }
}
