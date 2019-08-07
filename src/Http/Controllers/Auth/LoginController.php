<?php

namespace Seatplus\Web\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Seatplus\Web\Http\Controllers\Controller;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Seatplus\Web\Exceptions\SettingException
     */
    public function showLoginForm()
    {

        // Warn if SSO has not been configured yet.
        if (strlen(env('EVE_CLIENT_SECRET')) < 5 || strlen(env('EVE_CLIENT_ID')) < 5)
            session()->flash('warning', trans('web::auth.sso_config_warning'));

        return view('web::auth.login');
    }
}
