<?php


namespace Seatplus\Web\Services\ACL;


use Seatplus\Auth\Models\Permissions\Role;

class SyncRoleName
{
    /**
     * @var \Seatplus\Auth\Models\Permissions\Role
     */
    private $role;

    /**
     * SyncRoleName constructor.
     *
     * @param \Seatplus\Auth\Models\Permissions\Role $role
     */
    public function __construct(Role $role)
    {

        $this->role = $role;
    }

    public function sync(string $name)
    {

        if($this->role->name !== $name){
            $this->role->name = $name;
            $this->role->save();
        }
    }

}
