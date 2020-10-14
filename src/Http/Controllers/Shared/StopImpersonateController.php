<?php


namespace Seatplus\Web\Http\Controllers\Shared;


use Seatplus\Web\Http\Controllers\Controller;

class StopImpersonateController extends Controller
{
    public function __invoke()
    {

        // If there is no user set in the session, abort!
        abort_unless(session()->has('impersonation_origin'), 404);

        // Login
        auth()->login(session('impersonation_origin'));

        $route = session('route');

        // Clear the session value
        session()->forget(['impersonation_origin', 'route']);

        return redirect()->to($route)->with('success', 'Stopped Impersonate');
    }

}
