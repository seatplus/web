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

        abort_unless($user, 404, 'required url parameter user_id is missing');

        $affiliated_ids = getAffiliatedIdsByPermission($permission);

        $application = Applications::whereIn('corporation_id', $affiliated_ids)
            ->where(['applicationable_type' => User::class, 'applicationable_id' => $user->id])
            ->with('applicationable')
            ->first();

        abort_unless($application, 403, 'You are not allowed to review the user');

        return $next($request);
    }

}
