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
        try {
            return [
                'queue_count' => collect(resolve(WorkloadRepository::class)->get())
                    ->sum('length'),
                'error_count' => app(JobRepository::class)->countRecentlyFailed(),
                'status' => $this->currentStatus(),
            ];
        } catch (\Throwable) {
            // Best-effort widget: Horizon's workload/wait-time calc needs a real queue driver and a
            // running Horizon. Under a faked or unavailable queue (tests use Queue::fake(), which has
            // no RedisQueue::readyNow()) it throws — degrade to empty stats rather than 500 the page.
            return ['queue_count' => 0, 'error_count' => 0, 'status' => 'inactive'];
        }
    }

    private function currentStatus(): string
    {
        if (! $masters = app(MasterSupervisorRepository::class)->all()) {
            return 'inactive';
        }

        return collect($masters)->contains(fn (object $master) => $master->status === 'paused') ? 'paused' : 'running';
    }
}
