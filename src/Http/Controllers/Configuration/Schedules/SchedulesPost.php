<?php


namespace Seatplus\Web\Http\Controllers\Configuration\Schedules;


use Seatplus\Eveapi\Models\Schedules;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\PostSchedulesValidation;

class SchedulesPost extends Controller
{
    public function __invoke(PostSchedulesValidation $request)
    {
        Schedules::updateOrCreate([
            'job' => $request->job,
        ],[
            'expression' => $request->schedule
        ]);

        return redirect()->route('schedules.index')->with('success', 'Schedule updated or created');
    }

}
