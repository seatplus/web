<?php


namespace Seatplus\Web\Services\Sidebar;


use Illuminate\Support\Arr;
use Seatplus\Auth\Models\Permissions\Permission;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class SidebarEntries
{
    public function filter() : array
    {
        return collect(config('package.sidebar'))
            ->map(function ($topic) {
                return collect($topic)->reject(function ($entry) {
                    return Arr::has($entry, 'permission') ? $this->checkUserPermission($entry) : false;
                });
            })
            ->reject(function ($topic) {
                return $topic->isEmpty();
            })
            ->toArray();
    }

    private function checkUserPermission(array $array) :bool
    {
        $permission_name = Arr::get($array, 'permission');

        try {

            return ! auth()->user()->can($permission_name);
        } catch (PermissionDoesNotExist $exception) {

            Permission::create(['name' => $permission_name]);

            return $this->checkUserPermission($array);
        }

    }

}
