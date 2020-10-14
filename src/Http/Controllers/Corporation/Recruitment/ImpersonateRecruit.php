<?php


namespace Seatplus\Web\Http\Controllers\Corporation\Recruitment;


use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\ImpersonateService;

class ImpersonateRecruit extends Controller
{

    public function __invoke(User $recruit)
    {

        (new ImpersonateService)->impersonateUser($recruit);

        return redirect()->route('home')->with('success', 'Impersonating ' . $recruit->main_character->name);
    }

}
