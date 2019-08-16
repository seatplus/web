<?php

namespace Seatplus\Web\Http\Controllers;

use Inertia\Inertia;

class HomeController extends Controller
{
    public function home()
    {
        return view('web::home');
    }

    public function inertia()
    {
        return Inertia::render('Auth/Login');
    }
}
