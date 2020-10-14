<?php


namespace Seatplus\Web\Http\Controllers\Corporation\Recruitment;


use Inertia\Inertia;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Controllers\Controller;

class GetUserApplicationController extends Controller
{

    public function __invoke(User $recruit)
    {

        //dd(request()->route()->parameters());

        return Inertia::render('Corporation/Recruitment/Application', [
            'recruit' => $recruit->loadMissing('main_character', 'characters')
        ]);
    }

}
