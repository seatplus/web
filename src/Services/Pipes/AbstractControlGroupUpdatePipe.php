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

namespace Seatplus\Web\Services\Pipes;

use Illuminate\Support\Arr;
use Seatplus\Auth\Enums\RoleMembershipStatus;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Auth\Services\Roles\ManualRoleService;
use Seatplus\Web\Container\ControlGroupUpdateData;

abstract class AbstractControlGroupUpdatePipe implements ControlGroupUpdatePipe
{
    public function handleMember(ControlGroupUpdateData $data): void
    {
        $service = new ManualRoleService($data->role);
        $member_ids = collect($data->members)->pluck('id')->map(fn ($id) => (int) $id);

        // Remove members no longer selected
        $data->role->role_memberships()
            ->where('entity_type', User::class)
            ->whereNotIn('entity_id', $member_ids->toArray())
            ->cursor()
            ->each(fn ($membership) => $service->removeMember(User::find($membership->entity_id)));

        // Add new members (skip those already present)
        $current_member_ids = $data->role->fresh()
            ->role_memberships()
            ->where('entity_type', User::class)
            ->pluck('entity_id');

        collect($data->members)
            ->reject(fn ($member) => in_array((int) Arr::get($member, 'id'), $current_member_ids->toArray()))
            ->reject(fn ($member) => Arr::has($member, 'status') && $member['status'] !== 'member')
            ->each(fn ($member) => $service->addMember(User::find((int) Arr::get($member, 'id'))));
    }

    public function handleAffiliations(ControlGroupUpdateData $data): void
    {
        $entity_sets = collect($data->affiliations ?? [])
            ->map(fn ($affiliation) => [
                (int) $affiliation['id'],
                $affiliation['category'],
                'allowed',
            ])
            ->values()
            ->toArray();

        BaseRoleService::make($data->role)
            ->getTypeService()
            ->syncAffiliateManyEntities($entity_sets);
    }

    public function cleanWaitlist(ControlGroupUpdateData $data): void
    {
        $data->role->role_memberships()
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->delete();
    }

    public function removeModerators(ControlGroupUpdateData $data): void
    {
        $data->role->role_memberships()
            ->where('can_moderate', true)
            ->delete();
    }
}
