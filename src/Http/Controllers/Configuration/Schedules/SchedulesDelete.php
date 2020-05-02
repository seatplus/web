<?php


namespace Seatplus\Web\Http\Controllers\Configuration\Schedules;


use Seatplus\Eveapi\Models\Schedules;
use Seatplus\Web\Http\Controllers\Controller;

class SchedulesDelete extends Controller
{
    public function __invoke(int $id)
    {
        Schedules::find($id)->delete();

        return redirect()->route('schedules.index')->with('success', 'deleted');
    }

}
