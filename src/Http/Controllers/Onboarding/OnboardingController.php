<?php

namespace Seatplus\Web\Http\Controllers\Onboarding;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Response;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
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

        // The user's own open applications, keyed by corporation, so each enlistment
        // can show whether it has been applied to — replacing the former client-side
        // fetch of the list.existing.applications endpoint (useLoadCompleteResource).
        $applications = $this->openApplicationsByCorporation();

        $enlistments = Enlistment::query()
            ->with(['corporation' => ['alliance']])
            ->get()
            ->each(fn (Enlistment $enlistment) => $enlistment->setAttribute(
                'applications',
                $applications->get($enlistment->corporation_id, collect())->values(),
            ));

        return inertia('Onboarding/Index', [
            'step' => intval($request->query('step', 1)),
            'characters' => $user->characterUsers,
            'mainCharacterId' => $user->main_character_id,
            'enlistments' => $enlistments,
        ]);
    }

    /**
     * The authenticated user's open applications (account-wide or per owned
     * character), grouped by corporation_id.
     */
    private function openApplicationsByCorporation(): Collection
    {
        return Application::query()
            ->with('applicationable')
            ->whereHasMorph(
                'applicationable',
                [User::class, CharacterInfo::class],
                function (Builder $query, string $type) {
                    match ($type) {
                        User::class => $query->where('id', auth()->user()->getAuthIdentifier()),
                        CharacterInfo::class => $query->whereIn('character_id', auth()->user()->characters()->pluck('character_infos.character_id')),
                        default => null,
                    };

                    $query->where('status', 'open');
                },
            )
            ->get()
            ->groupBy('corporation_id');
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
