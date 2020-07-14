<?php


namespace Seatplus\Web\Http\Controllers\AccessControl;


use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Controllers\Controller;

class ListUserController extends Controller
{

    public function __invoke()
    {
        return User::with('main_character')->paginate();
    }

}
