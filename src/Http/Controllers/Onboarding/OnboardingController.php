<?php

namespace Seatplus\Web\Http\Controllers\Onboarding;

use Illuminate\Http\Request;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Models\Onboarding;
use Seatplus\Web\Models\Recruitment\Enlistment;

class OnboardingController extends Controller
{
    public function index(Request $request)
    {
        throw_unless(config('web.config.ONBOARDING'), 'Onboarding is disabled');

        $user = User::query()
            ->with('character_users')
            ->firstWhere('id', auth()->user()->getAuthIdentifier());

        return inertia('Onboarding/Index', [
            'step' => intval($request->query('step', 1)),
            'characters' => $user->character_users,
            'mainCharacterId' => $user->main_character_id,
            'enlistments' => Enlistment::query()
                ->with(['corporation' => ['alliance']])
                ->get(),
        ]);
    }

    public function complete()
    {
        // create onboarding record
        Onboarding::query()->create([
            'user_id' => auth()->user()->getAuthIdentifier(),
        ]);

        return redirect()->route('home');
    }
}
