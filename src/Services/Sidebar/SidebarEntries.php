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
use Seatplus\Auth\Models\Permissions\Permission;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class SidebarEntries
{
    public function filter(): array
    {
        return collect(config('package.sidebar'))
            ->map(function ($topic) {
                return collect($topic)->reject(function ($entry) {
                    return Arr::has($entry, 'permission') ? $this->checkUserPermission($entry) : false;
                });
            })
            ->reject(function ($topic) {
                return $topic->isEmpty();
            })
            ->toArray();
    }

    private function checkUserPermission(array $array): bool
    {
        $permission_name = Arr::get($array, 'permission');

        try {

            return ! auth()->user()->can($permission_name);
        } catch (PermissionDoesNotExist $exception) {

            Permission::create(['name' => $permission_name]);

            return $this->checkUserPermission($array);
        }

    }
}
