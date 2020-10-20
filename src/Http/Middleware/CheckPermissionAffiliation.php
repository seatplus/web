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
    public function handle(Request $request, Closure $next, string $permission)
    {

        if(auth()->user()->can('superuser'))
            return $next($request);

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        $url_parameters = $request->route()->parameters();

        $requested_id = collect([
            Arr::get($url_parameters,'character_id'),
            Arr::get($url_parameters,'corporation_id'),
            Arr::get($url_parameters,'alliance_id')
        ])->filter();

        if($requested_id->isEmpty()) {

            abort_unless($this->checkUserPermissions($permissions), 403, 'You do not have the necessary permission to perform this action.');

            return $next($request);
        }

        $affiliated_ids = $this->buildAffiliatedIdsByPermissions($permissions);

        //dump($affiliated_ids, $requested_id);

        abort_unless($requested_id->intersect($affiliated_ids)->isNotEmpty(), 403, 'You are not allowed to access the requested entity');

        return $next($request);

    }

    private function checkUserPermissions(array $permissions) : bool
    {
        foreach ($permissions as $permission) {
            if (auth()->user()->can($permission)) {
                return true;
            }
        }

        return false;
    }

    private function buildAffiliatedIdsByPermissions(array $permissions) : array
    {
        $ids = collect();

        foreach ($permissions as $permission) {

            $affiliatedIds = getAffiliatedIdsByPermission($permission);

            $ids->push($affiliatedIds);

            if($permission === 'can accept or deny applications')
                $ids->push($this->getRecruitIds($affiliatedIds));

        }

        return $ids->flatten()->unique()->toArray();
    }

    private function getRecruitIds(array $affiliated_ids) : array
    {

        $applicant_ids = Applications::whereIn('corporation_id', $affiliated_ids)
            ->pluck('applicationable_id')
            ->toArray();

        return $applicant_ids;
    }


}
