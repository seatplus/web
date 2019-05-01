<?php


namespace Seatplus\Web\Http\Controllers;


class HomeController extends Controller
{
    public function home()
    {
        return view('web::test');
    }

}