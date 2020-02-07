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

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Spatie\Permission\PermissionRegistrar;

class ManageControllGroupMembersController
{
    public function index($role_id)
    {
        $users = User::all()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->main_character->name,
                'character_id' => $user->main_character->character_id,
                'characters' => $user->characters->filter(function ($character) use ($user) {
                    return $character->character_id !== $user->main_character->character_id;
                }),
            ];
        });

        return Inertia::render('AccessControl/ManageControlGroup', [
            'users' => $users,
            'members' => Role::findById($role_id)->users()->get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->main_character->name,
                    'character_id' => $user->main_character->character_id,
                    'characters' => $user->characters->filter(function ($character) use ($user) {
                        return $character->character_id !== $user->main_character->character_id;
                    }),
                ];
            }),
        ]);
    }

    public function update(Request $request, $role_id)
    {

        $validated_data = $request->validate([
            'selectedValues.*.id' => 'bail|integer|exists:users,id',
        ]);

        $role = Role::findById($role_id);

        $users_should_have_role = empty($validated_data) ? collect() : collect($validated_data['selectedValues'])->map(function ($vaule) {
            return Arr::get($vaule, 'id');
        });

        $role->users()->sync($users_should_have_role->toArray());

        //Update Cache
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()
            ->action([ManageControllGroupMembersController::class, 'index'], $role_id)
            ->with('Success', 'Control Group updated');

    }
}
