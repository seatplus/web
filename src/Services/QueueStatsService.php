<?php

declare(strict_types=1);

namespace Seatplus\Web\Services;

use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\Contracts\WorkloadRepository;

class QueueStatsService
{
    /**
     * @return array{queue_count: int, error_count: int, status: string}
     */
    public function get(): array
    {
        return [
            'queue_count' => collect(resolve(WorkloadRepository::class)->get())
                ->sum('length'),
            'error_count' => app(JobRepository::class)->countRecentlyFailed(),
            'status' => $this->currentStatus(),
        ];
    }

    private function currentStatus(): string
    {
        if (! $masters = app(MasterSupervisorRepository::class)->all()) {
            return 'inactive';
        }

        return collect($masters)->contains(fn (object $master) => $master->status === 'paused') ? 'paused' : 'running';
    }
}
