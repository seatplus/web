<?php


namespace Seatplus\Web\Http\Controllers\Configuration\Schedules;


use Inertia\Inertia;
use Seatplus\Eveapi\Models\Schedules;
use Seatplus\Web\Http\Controllers\Controller;

class ScheduleDetail extends Controller
{
    public function __invoke($schedule_id)
    {

        return Inertia::render('Configuration/Schedules/SchedulesDetails', [
            'schedule' => Schedules::find($schedule_id),
            'cron' => config('web.cronExpressions')
        ]);
    }

}
