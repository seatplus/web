<?php


namespace Seatplus\Web\Http\Controllers\Configuration\Schedules;

use Inertia\Inertia;
use Seatplus\Web\Http\Controllers\Controller;

class SchedulesCreate extends  Controller
{
    public function __invoke()
    {

        $cron = config('web.cronExpressions');

        return Inertia::render('Configuration/Schedules/SchedulesCreate', [
            'cron' => $cron,
            'jobs' => config('eveapi.updateJobs')
        ]);
    }
}
