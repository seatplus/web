<?php


namespace Seatplus\Web\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Applications;

class CheckPermissionAffiliation
{
    /**
     *
     * @param Closure $next
     * @param $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {

        /*if(auth()->user()->can('superuser'))
            return $next($request);*/

        $url_parameters = $request->route()->parameters();

        $requested_id = collect([
            Arr::get($url_parameters,'character_id'),
            Arr::get($url_parameters,'corporation_id'),
            Arr::get($url_parameters,'alliance_id')
        ])->filter();

        if($requested_id->isEmpty())
            return redirect()->back()->with('error', 'required url parameter (character_id, corporation_id or alliance_id) is missing');

        $affiliated_ids = getAffiliatedIdsByPermission($permission);

        return $requested_id->intersect($affiliated_ids)->isNotEmpty()
            ? $next($request)
            : redirect()->back()->with('error', 'You are not allowed to access the requested entity');
    }

}
