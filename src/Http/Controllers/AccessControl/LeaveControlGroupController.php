<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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

        if(! in_array($this->role->type, ['opt-in', 'on-request']))
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

    private function isSuperuserOrModerator(): bool
    {
        return auth()->user()->can('superuser') || $this->role->isModerator(auth()->user());
    }

    private function isActionOnYourself(): bool
    {
        return auth()->user()->getAuthIdentifier() === $this->user->id;
    }

    private function illegalAction()
    {
        return abort(403, 'You are not allowed to perform this action');
    }
}
