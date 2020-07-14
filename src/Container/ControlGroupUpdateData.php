<?php


namespace Seatplus\Web\Container;


use Seatplus\Auth\Models\Permissions\Role;
use Spatie\DataTransferObject\DataTransferObject;

class ControlGroupUpdateData extends DataTransferObject
{
    public Role $role;

    public string $role_type;

    public ?array $affiliations;

    public ?array $members;

}
