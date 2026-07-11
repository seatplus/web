<?php

use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Bus;
use Seatplus\Eveapi\Jobs\Wallet\CharacterBalanceJob;
use Seatplus\Web\Jobs\ManualDispatchedJob;

it('dispatches the manual update batch with allowFailures so one failing job does not cancel the rest', function () {
    Bus::fake();

    (new ManualDispatchedJob)
        ->setJobs([new CharacterBalanceJob(1234)])
        ->setName('Manual batch update of wallet:1234')
        ->handle();

    Bus::assertBatched(fn (PendingBatch $batch): bool => $batch->allowsFailures()
        && $batch->name === 'Manual batch update of wallet:1234');
});
