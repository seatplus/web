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

declare(strict_types=1);

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Models\Permissions\Affiliation;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Web\Http\Controllers\Controller;

class ShowControlGroupController extends Controller
{
    public function __construct(
        private readonly BaseRoleService $baseRoleService,
    ) {}

    public function __invoke(int $role_id): Response|RedirectResponse
    {
        $role = Role::with('affiliations.affiliatable', 'role_memberships.entity', 'permissions')
            ->findOrFail($role_id);

        $user = auth()->user();

        abort_unless(
            $user->can('superuser')
                || $user->can('administrate access control groups')
                || $this->baseRoleService->for($role)->canModerate($user),
            403
        );

        return Inertia::render('AccessControl/RoleDetail', [
            'role' => [
                'id' => $role->id,
                'name' => $role->name,
                'type' => $role->type->value,
                'affiliations' => $role->affiliations->map(fn (Affiliation $affiliation) => [
                    'id' => $affiliation->affiliatable_id,
                    'category' => $affiliation->affiliatable_type,
                    'type' => $affiliation->type,
                ]),
                'permissions' => $role->permissions->pluck('name'),
            ],
            'activeSidebarElement' => 'acl.groups',
        ]);
    }
}
