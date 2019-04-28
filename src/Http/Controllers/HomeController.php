<?php


namespace Seatplus\Web\Http\Controllers;


use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    public function test()
    {
        return view('web::test');
    }

}