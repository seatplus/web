<?php

namespace Seatplus\Web\Http\Controllers\Recruitment;

use Illuminate\Http\RedirectResponse;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Http\Controllers\Controller;

/**
 * Withdraw one of the signed-in user's own applications from the job portal.
 */
class WithdrawController extends Controller
{
    public function __invoke(string $application_id): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $application = Application::query()->findOrFail($application_id);

        abort_unless($this->belongsToUser($user, $application), 403, 'This is not your application.');

        $application->delete();

        return back()->with('success', 'Application withdrawn');
    }

    private function belongsToUser(User $user, Application $application): bool
    {
        return match ($application->applicationable_type) {
            User::class => (int) $application->applicationable_id === (int) $user->getAuthIdentifier(),
            CharacterInfo::class => in_array($application->applicationable_id, $user->characters->pluck('character_id')->toArray()),
            default => false,
        };
    }
}
