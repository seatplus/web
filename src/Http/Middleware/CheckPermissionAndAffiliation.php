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

        if(collect($request->route()->parameters())->intersectByKeys([
            'character_id' => '', 'corporation_id' => '', 'alliance_id' => ''
        ])->isEmpty())
            abort(403, 'Missing required route parameter.');


        // if the character belongs to the user, no permissions are required.
        if($request->character_id && in_array($request->character_id, auth()->user()->characters->pluck('character_id')->toArray()))
            return $next($request);

        // if the character or corporation is not provided by the user, check permissions
        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        foreach ($permissions as $permission) {
            /*
             * If we think about corporation roles such as director or accountant we should check directPermissions
             * TODO: create direct permissions to users within CharacterRoles job
             * TODO: if refreshtoken is revoked remove all direct permissions
            */

            // start by asserting that the user has the required permission
            if (app('auth')->user()->hasPermissionTo($permission)) {

                // Check a users roles and only take the ones into account which have a certain permission
                $users_roles_with_permission = app('auth')->user()->roles->filter(function ($role) use ($permission) {
                    return $role->hasPermissionTo($permission);
                });

                // Check if role has an affiliation to the requested resource
                $users_roles_with_permission_and_affiliation = $users_roles_with_permission->isNotEmpty()
                    ? $users_roles_with_permission->filter(function ($role) use ($request) {

                        /*
                         * First we assume that there is always just one route parameter to check:
                         * either character_id, corporation_id or alliance_id is supplied. As a result
                         * the first id is taken from the parameters array and converted to an integer.
                         */
                        $id_to_check_affiliation = ($id = reset($request->route()->parameters)) ? intval($id) : null;

                        if(is_integer($id_to_check_affiliation))
                            return $role->isAffiliated($id_to_check_affiliation);

                        return false;
                    })
                    : collect();

                // if a role has both permission and affiliation let user see the page
                if($users_roles_with_permission_and_affiliation->isNotEmpty())
                    return $next($request);
            }
        }

        return abort(403, 'Missing Permission: ' . $permission);
    }
}
