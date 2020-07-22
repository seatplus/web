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

use Seatplus\Auth\Models\AccessControl\AclMember;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\UserRessource;

class ListMembersController extends Controller
{
    public function __invoke(int $role_id)
    {
        $role = Role::find($role_id);

        abort_unless($role->isModerator(auth()->user()), 403);

        $users = User::query()
            ->join('acl_members', fn ($join) => (
            $join->on('users.id', '=', 'acl_members.user_id')
                ->where('acl_members.role_id', $role_id)
            ))
            ->addSelect([
                'status' => AclMember::select('status')->whereColumn('user_id', 'users.id')
                    ->where('role_id', '=', $role_id)
                    ->limit(1),
            ]);

        return UserRessource::collection(
            $users->paginate()
        );
    }
}
