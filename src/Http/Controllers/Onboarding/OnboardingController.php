<?php

namespace Seatplus\Web\Http\Controllers\Onboarding;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Models\Onboarding;
use Seatplus\Web\Models\Recruitment\Enlistment;

class OnboardingController extends Controller
{
    public function index(Request $request): Response
    {
        throw_unless(config('web.config.ONBOARDING'), 'Onboarding is disabled');

        $user = User::query()
            ->with('characterUsers')
            ->firstWhere('id', auth()->user()->getAuthIdentifier());

        return inertia('Onboarding/Index', [
            'step' => intval($request->query('step', 1)),
            'characters' => $user->characterUsers,
            'mainCharacterId' => $user->main_character_id,
            'enlistments' => Enlistment::query()
                ->with(['corporation' => ['alliance']])
                ->withOpenApplicationsOf(auth()->user())
                ->get(),
        ]);
    }

    public function complete(): RedirectResponse
    {
        // create onboarding record
        Onboarding::query()->create([
            'user_id' => auth()->user()->getAuthIdentifier(),
        ]);

        return redirect()->route('home');
    }
}
