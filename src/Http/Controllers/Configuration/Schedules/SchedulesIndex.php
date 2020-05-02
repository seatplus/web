<?php


namespace Seatplus\Web\Http\Controllers\Configuration\Schedules;

use Inertia\Inertia;
use Seatplus\Eveapi\Models\Schedules;
use Seatplus\Web\Http\Controllers\Controller;

class SchedulesIndex extends  Controller
{
    public function __invoke()
    {
        return Inertia::render('Configuration/Schedules/SchedulesIndex', [
            'schedules' => Schedules::all(),
            'expressions' => config('web.cronExpressions')
        ]);
    }
}
