<?php

namespace Seatplus\Web\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class LoginController
{
    public function __invoke(): Response|RedirectResponse
    {
        // redirect to /home when authenticated

        if (auth()->check()) {
            return redirect()->route('home');
        }

        // Warn if SSO has not been configured yet.
        if (strlen(config('web.config.EVE_CLIENT_ID')) < 5 || strlen(config('web.config.EVE_CLIENT_SECRET')) < 5) {
            session()->flash('warning', trans('web::auth.sso_config_warning'));
        }

        return Inertia::render('Auth/Login', [
            'evesso_img_src' => asset('img/evesso.png'),
        ]);
    }
}
