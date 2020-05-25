<?php


namespace Seatplus\Web\Http\Controllers\Queue;


use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Eveapi\Services\DispatchIndividualUpdate;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\DispatchIndividualJob;

class DispatchJobController extends Controller
{
    public function __invoke(DispatchIndividualJob $job)
    {

        $refresh_token = RefreshToken::find($job['character_id']);

        $job_id = (new DispatchIndividualUpdate($refresh_token))->execute($job['job']);

        $cache_key = sprintf('%s:%s', $job['job'], $job['character_id']);

        cache([$cache_key => $job_id], now()->addHour());

        return redirect()->back()->with('success', 'job queued');

    }

}
