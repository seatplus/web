<?php

namespace Seatplus\Web\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Models\Onboarding;

class OnboardingMiddleware
{
    private $user;

    public function handle(Request $request, Closure $next)
    {
        // if onboarding is disabled, skip middleware
        if (! config('web.config.ONBOARDING')) {
            return $next($request);
        }

        $this->setUser($request);

        // if user has been created longer then an hour ago don't bother with onboarding
        if ($this->getUser()->created_at->diffInMinutes(now()) >= 60) {
            return $next($request);
        }

        // if user has completed onboarding don't bother with onboarding
        if (Onboarding::firstWhere('user_id', $this->getUser()->getAuthIdentifier())) {
            return $next($request);
        }

        return redirect()->route('onboarding');
    }

    private function setUser(Request $request): void
    {
        $this->user = $request->user();
    }

    private function getUser(): User
    {
        return $this->user;
    }
}
