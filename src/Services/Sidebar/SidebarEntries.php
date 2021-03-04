<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
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
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class SidebarEntries
{

    private array $sidebar;

    public function __construct(
        private ?User $user = null
    )
    {
        $this->user ??= auth()->user();
        $this->sidebar = config('package.sidebar');
    }

    public function filter()
    {
        $categories = array_keys($this->sidebar);

        return collect($categories)->mapWithKeys(function ($category) {
            $entries = $this->buildAvailableSidebarEntriesArray($category);

            return empty($entries)
                ? [$category => null]
                : [
                    $category => [
                        'name' => $category,
                        'entries' => $entries
                    ]
                ];
        })->filter();
    }

    private function checkPermission(string $permission): bool
    {
        try {
            return $this->user->can($permission);
        } catch (PermissionDoesNotExist) {
            Permission::create(['name' => $permission]);

            return $this->checkPermission($permission);
        }
    }

    private function buildAvailableSidebarEntriesArray(string $category) : array
    {
        $entries = Arr::get($this->sidebar, $category);

        return collect($entries)->filter(function($entry) {

            $permission = Arr::get($entry, 'permission');
            $character_role = Arr::get($entry, 'character_role');

            // if entry has no required permission show it to the user
            if(is_null($permission))
                return true;

            // if user has required permission show element
            if($this->checkPermission($permission))
                return true;

            // if user does not have permission but got necessary character_role show element
            return is_string($character_role) ? $this->hasUserCharacterRole($character_role) : false;

        })->toArray();
    }

    /*
    * Checks if user has required role
    */
    private function hasUserCharacterRole(string $character_role): bool
    {

        return $this->user
            ->loadMissing('characters.roles')
            ->characters
            ->map(fn ($character) => $character->roles->hasRole('roles', Str::ucfirst($character_role)))
            ->filter()
            ->isNotEmpty();
    }
}
