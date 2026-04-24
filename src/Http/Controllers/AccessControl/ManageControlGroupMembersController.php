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

namespace Seatplus\Web\Http\Controllers\AccessControl;

use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

class ManageControlGroupMembersController
{
    public function index(int $role_id): Response
    {
        $role = Role::whereId($role_id)
            ->with(
                'affiliations.affiliatable',
                'role_memberships.entity',
            )
            ->first();

        $memberships = $role->role_memberships->where('entity_type', User::class);

        $mappedRole = [
            'title' => $role->name,
            'id' => $role->id,
            'type' => $role->type->value,
            'acl' => [
                'affiliations' => $role->affiliations->map(fn (mixed $affiliation) => [
                    'id' => $affiliation->affiliatable_id,
                    'type' => [
                        CorporationInfo::class => 'corporation',
                        AllianceInfo::class => 'alliance',
                    ][$affiliation->affiliatable_type],
                ]),
                'moderators' => $memberships
                    ->where('can_moderate', true)
                    ->map(fn (RoleMembership $membership) => $membership->entity),
                'members' => $memberships->map(function (RoleMembership $membership) {
                    $membership->id = $membership->entity_id;

                    return $membership;
                }),
            ],
        ];

        return Inertia::render('AccessControl/ManageControlGroup', [
            'role' => $mappedRole,
            'activeSidebarElement' => 'acl.groups',
        ]);
    }
}
