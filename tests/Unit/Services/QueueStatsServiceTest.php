<?php

use Laravel\Horizon\Contracts\WorkloadRepository;
use Seatplus\Web\Services\QueueStatsService;

it('degrades to empty stats when the Horizon workload cannot be computed', function () {
    // Horizon's WaitTimeCalculator needs a real queue driver; under Queue::fake() it calls
    // QueueFake::readyNow() (which does not exist) and throws. The optional settings-page widget
    // must degrade rather than 500 the whole Inertia response.
    $mock = Mockery::mock(WorkloadRepository::class);
    $mock->shouldReceive('get')->andThrow(new BadMethodCallException('Call to undefined method readyNow()'));
    app()->instance(WorkloadRepository::class, $mock);

    expect(app(QueueStatsService::class)->get())
        ->toBe(['queue_count' => 0, 'error_count' => 0, 'status' => 'inactive']);
});
