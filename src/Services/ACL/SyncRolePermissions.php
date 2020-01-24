<?php


namespace Seatplus\Web\Services\ACL;


use Illuminate\Support\Arr;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class SyncRolePermissions
{
    /**
     * @var \Seatplus\Auth\Models\Permissions\Role
     */
    private $role;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $current_permissions;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $target_permissions;

    public function __construct(Role $role)
    {
        $this->role = $role;
        $this->current_permissions = $role->permissions()->pluck('name');
        $this->target_permissions = collect();
    }

    public function sync(array $validated_data)
    {
        $permissions = Arr::get($validated_data, 'permissions', null);

        if($permissions)
        {
            foreach ($permissions as $permission){
                $name = $permission['name'];

                $this->target_permissions->push($name);
                try {
                    $this->role->givePermissionTo($name);
                } catch (PermissionDoesNotExist $exception) {
                    Permission::create(['name' => $name]);

                    $this->role->givePermissionTo($name);
                }

            }
        }

        $this->removeUnassignedPermissions();
    }

    private function removeUnassignedPermissions()
    {
        $this->current_permissions->diff($this->target_permissions)->each(function ($to_be_removed_permissions) {
            return $this->role->revokePermissionTo($to_be_removed_permissions);
        });
    }

}
