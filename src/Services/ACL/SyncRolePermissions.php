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

namespace Seatplus\Web\Services\ACL;

use Illuminate\Support\Arr;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class SyncRolePermissions
{
    /**
     * @var \Seatplus\Auth\Models\Permissions\Role
     */
    private $role;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $current_permissions;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $target_permissions;

    public function __construct(Role $role)
    {
        $this->role = $role;
        $this->current_permissions = $role->permissions()->pluck('name');
        $this->target_permissions = collect();
    }

    public function sync(array $validated_data)
    {
        $permissions = Arr::get($validated_data, 'permissions', null);

        if ($permissions) {
            foreach ($permissions as $permission) {
                $name = $permission;

                $this->target_permissions->push($name);
                try {
                    $this->role->givePermissionTo($name);
                } catch (PermissionDoesNotExist $exception) {
                    Permission::create(['name' => $name]);

                    $this->role->givePermissionTo($name);
                }
            }
        }

        $this->removeUnassignedPermissions();
    }

    private function removeUnassignedPermissions()
    {
        $this->current_permissions->diff($this->target_permissions)->each(function ($to_be_removed_permissions) {
            return $this->role->revokePermissionTo($to_be_removed_permissions);
        });
    }
}
