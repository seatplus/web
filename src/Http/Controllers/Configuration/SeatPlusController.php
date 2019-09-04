<?php

namespace Seatplus\Web\Http\Controllers\Configuration;

use Inertia\Inertia;
use Seatplus\Web\Http\Controllers\Controller;

class SeatPlusController extends Controller
{
    public function settings()
    {
        return Inertia::render('Configuration/Settings');
    }
}