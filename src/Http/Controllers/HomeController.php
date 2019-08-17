<?php

namespace Seatplus\Web\Http\Controllers;

use Inertia\Inertia;

class HomeController extends Controller
{
    public function home()
    {
        return Inertia::render('Dashboard/Index');
    }
}
