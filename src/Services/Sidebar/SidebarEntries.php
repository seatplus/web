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
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class SidebarEntries
{
    private const CATEGORY_ACCESS_CONTROL = 'Access Control';

    private array $sidebar;

    private readonly SidebarPermissionChecker $permissionChecker;

    public function __construct(
        private ?User $user = null
    ) {
        $this->user ??= auth()->user();
        $this->permissionChecker = new SidebarPermissionChecker($this->user);
        $this->sidebar = $this->initializeSidebar();
    }

    public function getFilteredEntries(): Collection
    {
        return collect($this->sidebar)->map(function (mixed $entries, mixed $category) {
            $availableEntries = $this->getAvailableEntries($entries, $category);

            if (empty($availableEntries)) {
                return null;
            }

            return [
                'name' => ucfirst($category),
                'entries' => $availableEntries,
            ];
        })->filter()->values();
    }

    private function getAvailableEntries(array $entries, string $category): array
    {
        return collect($entries)->filter(function (mixed $entry) use ($category) {
            $permissionString = Arr::get($entry, 'permission');
            $character_role = Arr::get($entry, 'character_role', '');

            if (is_null($permissionString)) {
                return true;
            }

            if ($this->permissionChecker->hasPermissionOrCorporationRole($permissionString, $character_role)) {
                return true;
            }

            if ($category === self::CATEGORY_ACCESS_CONTROL && $this->permissionChecker->isRoleModerator()) {
                return true;
            }

            return false;
        })->toArray();
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

    private function initializeSidebar(): array
    {
        $sidebar = config('package.sidebar');

        return collect($sidebar)->map(function (mixed $entries, mixed $category) {
            return collect($entries)->map(function (mixed $entry) {
                $entry['uri'] = route($entry['route']);

                return $entry;
            })->toArray();
        })->toArray();
    }
}
