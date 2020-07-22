<?php


namespace Seatplus\Web\Http\Controllers\AccessControl;



use Seatplus\Auth\Http\Controllers\Controller;
use Seatplus\Auth\Models\Permissions\Role;

class DeleteControlGroupController extends Controller
{
    public function __invoke(int $role_id)
    {

        Role::findById($role_id)->delete();

        return redirect()
            ->route('acl.groups')
            ->with('success', 'Access control group deleted');
    }

}
