<?php


namespace Seatplus\Web\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Applications;

class CheckUserAffiliationForApplication
{
    /**
     *
     * @param Closure $next
     * @param $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {

        $user = $request->recruit;

        if(!$user)
            return redirect()->back()->with('error', 'required url parameter user_id is missing');

        $affiliated_ids = getAffiliatedIdsByPermission($permission);

        $application = Applications::whereIn('corporation_id', $affiliated_ids)
            ->where('applicationable_type', User::class)
            ->with('applicationable')
            ->get()
            ->filter(fn($application) => $application->applicationable_id === $user->id)
            ->first();

        if(is_null($application))
            return redirect()->back()->with('error', 'You are not allowed to review the user');

        return $next($request);
    }

}
