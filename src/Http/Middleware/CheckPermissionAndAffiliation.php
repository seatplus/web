<?php

namespace Seatplus\Web\Http\Middleware;

use Closure;
use Seatplus\Web\Exceptions\UnauthorizedException;

class CheckPermissionAndAffiliation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
        if (app('auth')->guest()) {
            abort(403, 'User is not logged in.');
        }

        //$request->route()->parameters()
        if(collect($request->route()->parameters())->intersectByKeys([
            'character_id' => '', 'corporation_id' => '', 'alliance_id' => ''
        ])->isEmpty())
            abort(403, 'Missing required route parameter.');


        // if the character belongs to the user, no permissions are required.
        if($request->character_id && in_array($request->character_id, auth()->user()->characters->pluck('character_id')->toArray()))
            return $next($request);

        // TODO: if corporation data is provided by the users characters we should ignore permissions too.

        // if the character or corporation is not provided by the user, check permissions
        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        foreach ($permissions as $permission) {
            if (app('auth')->user()->hasPermissionTo($permission)) {

                $users_roles_with_permission = app('auth')->user()->roles->filter(function ($role) use ($permission) {
                    return $role->hasPermissionTo($permission);
                });

                $users_roles_with_permission_and_affiliation = $users_roles_with_permission->isNotEmpty()
                    ? $users_roles_with_permission->filter(function ($role) use ($request) {

                        $id_to_check_affiliation = ($id = reset($request->route()->parameters)) ? intval($id) : null;

                        var_dump($id_to_check_affiliation);

                        if(is_integer($id_to_check_affiliation))
                            return $role->isAffiliated($id_to_check_affiliation);

                        return false;
                    })
                    : collect();

                if($users_roles_with_permission_and_affiliation->isNotEmpty())
                    return $next($request);
            }
        }

        return abort(403, 'Missing Permission: ' . $permission);
    }
}
