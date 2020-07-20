<?php


namespace Seatplus\Web\Http\Controllers\AccessControl;


use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\JoinControlGroup;

class JoinControlGroupController extends Controller
{
    private Role $role;

    private User $user;

    public function __invoke(JoinControlGroup $request)
    {
        $this->role = Role::find($request->role_id);
        $this->user = User::find($request->user_id ?? auth()->user()->getAuthIdentifier());

        if(!in_array($this->role->type, ['opt-in', 'on-request']))
            return abort(403);

        (auth()->user()->can('superuser') || $this->role->isModerator(auth()->user()))
            ? $this->becomeMember() : $this->joinWaitlist();

        return redirect()->back();
    }

    private function becomeMember()
    {
        $this->role->activateMember($this->user);
    }

    private function joinWaitlist()
    {
        $this->role->joinWaitlist($this->user);
    }

}
