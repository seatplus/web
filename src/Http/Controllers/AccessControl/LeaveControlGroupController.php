<?php


namespace Seatplus\Web\Http\Controllers\AccessControl;


use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Controllers\Controller;

class LeaveControlGroupController extends Controller
{
    private Role $role;

    private User $user;

    public function __invoke(int $role_id, int $user_id)
    {
        $this->role = Role::find($role_id);
        $this->user = User::find($user_id);

        if(!in_array($this->role->type, ['opt-in', 'on-request']))
            return abort(403, 'This action is not allowed on this access control group');

        $this->isActionOnYourself()
            ? $this->removeMember()
            : ($this->isSuperuserOrModerator() ? $this->removeMember() : $this->illegalAction());

        return redirect()->back();
    }

    private function removeMember()
    {
        $this->role->removeMember($this->user);
    }

    private function isSuperuserOrModerator() : bool
    {
        return (auth()->user()->can('superuser') || $this->role->isModerator(auth()->user()));
    }

    private function isActionOnYourself() : bool
    {
        return auth()->user()->getAuthIdentifier() === $this->user->id;
    }

    private function illegalAction()
    {
        return abort(403, 'You are not allowed to perform this action');
    }
}
