<?php

namespace Seatplus\Web\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('auth.login');
        }
    }

    // TODO Implement protected function unauthenticated($request, array $guards) https://github.com/illuminate/auth/blob/master/Middleware/Authenticate.php#L79
}
