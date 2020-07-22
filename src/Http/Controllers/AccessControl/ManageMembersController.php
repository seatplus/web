<?php


namespace Seatplus\Web\Http\Controllers\AccessControl;


use Inertia\Inertia;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Web\Http\Controllers\Controller;

class ManageMembersController extends Controller
{
    public function __invoke(int $role_id)
    {
        $role = Role::find($role_id);

        abort_unless($role->isModerator(auth()->user()), 403);

        return Inertia::render('AccessControl/ManageMembers', [
            'role' => $role
        ]);
    }

}
