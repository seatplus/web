<?php

namespace Seatplus\Web\Http\Controllers\Configuration;

use Seatplus\Web\Http\Controllers\Controller;
use Inertia\Inertia;

class SeatPlusController extends Controller
{
    public function settings()
    {
        return Inertia::render('Configuration/Settings');
    }

}