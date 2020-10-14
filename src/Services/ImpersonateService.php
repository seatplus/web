<?php


namespace Seatplus\Web\Services;


use Seatplus\Auth\Models\User;

class ImpersonateService
{
    public function impersonateUser(User $user)
    {
        // Store the original user and return url in the session
        session([
            'impersonation_origin' => auth()->user(),
            'route' => url()->previous()
        ]);

        auth()->login($user);
    }
}
