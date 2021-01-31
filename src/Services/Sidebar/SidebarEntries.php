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

namespace Seatplus\Web\Services\Sidebar;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class SidebarEntries
{
    public function __construct()
    {
        $this->user = User::first();//auth()->user();
    }

    public function filter()
    {
        return collect(config('package.sidebar'))
            ->map(function ($topic) {
                return collect($topic)->reject(fn($entry) => Arr::has($entry, 'character_role')
                    ? $this->IsUserMissingCharacterRole($entry)
                    : (Arr::has($entry, 'permission') ? $this->isUserMissingPermission($entry) : false));
            })
            ->reject(fn($topic) => $topic->isEmpty())->map(function ($entries, $category) {
                return [
                    'name' => $category,
                    'entries' => $entries,
                ];
            });
    }

    private function isUserMissingPermission(array $array): bool
    {
        $permission_name = Arr::get($array, 'permission');

        try {
            return ! $this->user->can($permission_name);
        } catch (PermissionDoesNotExist) {
            Permission::create(['name' => $permission_name]);

            return $this->isUserMissingPermission($array);
        }
    }

    /*
    * Checks if user misses required permission and role
    */
    private function IsUserMissingCharacterRole($entry): bool
    {
        $role = Arr::get($entry, 'character_role');

        return $this->user
            ->loadMissing('characters.roles')
            ->characters
            ->map(fn ($character) => $character->roles->hasRole('roles', Str::ucfirst($role)))
            ->filter()
            ->isEmpty();
    }
}
