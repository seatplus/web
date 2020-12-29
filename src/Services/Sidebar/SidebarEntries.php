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
        $this->user = auth()->user();
    }

    public function filter()
    {
        return collect(config('package.sidebar'))
            ->map(function ($topic) {
                return collect($topic)->reject(function ($entry) {
                    return Arr::has($entry, 'character_role')
                        ? $this->checkUserCharacterRole($entry)
                        : (Arr::has($entry, 'permission') ? $this->checkUserPermission($entry) : false);
                });
            })
            ->reject(function ($topic) {
                return $topic->isEmpty();
            })->map(function ($entries, $category) {
                return [
                    'name' => $category,
                    'entries' => $entries,
                ];
            });
    }

    private function checkUserPermission(array $array): bool
    {
        $permission_name = Arr::get($array, 'permission');

        try {
            return ! $this->user->can($permission_name);
        } catch (PermissionDoesNotExist) {
            Permission::create(['name' => $permission_name]);

            return $this->checkUserPermission($array);
        }
    }

    private function checkUserCharacterRole($entry): bool
    {
        $role = Arr::get($entry, 'permission');

        return User::has('characters.roles')
            ->whereId($this->user->getAuthIdentifier())
            ->with('characters.roles')
            ->get()
            ->whenNotEmpty(fn ($collection) => $collection
                ->first()
                ->characters
                ->map(fn ($character) => $character->roles->hasRole('roles', Str::ucfirst($role)) ?? false)
                ->filter()
            )
            ->isNotEmpty();
    }
}
