<?php


namespace Seatplus\Web\Http\Controllers\Corporation\Recruitment;


use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Applications;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\ImpersonateService;

class ImpersonateRecruit extends Controller
{
    const RECRUITERPERMISSION = 'can accept or deny applications';

    public function __invoke(User $user)
    {
        $affiliated_ids = getAffiliatedIdsByPermission(self::RECRUITERPERMISSION);

        $application = Applications::whereIn('corporation_id', $affiliated_ids)
            ->where('applicationable_type', User::class)
            ->with('applicationable')
            ->get()
            ->filter(fn($application) => $application->id === $user->id)
            ->first();

        if(is_null($application))
            return redirect()->back()->with('error', 'You are not allowed to impersonate the user');

        (new ImpersonateService)->impersonateUser($user, 'corporation.recruitment');

        return redirect()->route('home')->with('success', 'Impersonating ' . $user->main_character->name);
    }

}
