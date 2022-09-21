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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Container\ControlGroupUpdateData;
use Seatplus\Web\Services\DispatchCorporationOrAllianceInfoJob;

abstract class AbstractControlGroupUpdatePipe implements ControlGroupUpdatePipe
{
    public function handleMember(ControlGroupUpdateData $data)
    {
        $member_ids = collect($data->members)->pluck('user_id');

        // First remove member no longer selected
        $data->role->members()
            ->whereNotIn('user_id', $member_ids->toArray())
            ->get()
            ->each(fn ($member) => $data->role->removeMember(User::find($member['user_id'])));

        // get missing members
        $current_member_ids = $data->role->fresh()->members()->pluck('user_id');

        collect($data->members)
            ->reject(fn ($member) => in_array(Arr::get($member, 'id'), $current_member_ids->toArray()))
            // remove members on waitlist or paused
            ->reject(fn ($member) => Arr::has($member, 'status') ? $member['status'] !== 'member' : false)
            ->each(fn ($member) => $data->role->activateMember(User::find(Arr::get($member, 'id'))));
    }

    public function handleAffiliations(ControlGroupUpdateData $data)
    {
        $affiliatable_ids = data_get($data, 'affiliations.*.id', []);

        // Delete removed affiliations
        $data->role
            ->acl_affiliations()
            ->whereNotIn('affiliatable_id', $affiliatable_ids)
            ->delete();

        // First get the affiliations to delete
        collect($data->affiliations)
            ->whenNotEmpty(function ($affiliations) use ($data) {
                $existing_ids = $data->role->acl_affiliations->map(fn ($affiliation) => $affiliation->affiliatable_id);

                collect($data->affiliations)
                    ->reject(fn ($affiliation) => in_array($affiliation['id'], $existing_ids->toArray()))
                    ->each(fn ($affiliation) => $data->role->acl_affiliations()->create([
                        'affiliatable_id' => $affiliation['id'],
                        'affiliatable_type' => $affiliation['category'] === 'corporation' ? CorporationInfo::class : AllianceInfo::class,
                    ]));

                return $affiliations;
            })
            ->whenEmpty(fn ($affiliations) => $data->role->acl_affiliations()->delete());

        $data->role->refresh()
            ->acl_affiliations()
            ->whereDoesntHaveMorph(
                'affiliatable',
                [CorporationInfo::class, AllianceInfo::class]
            )
            ->get()
            ->each(fn ($affiliation) => (new DispatchCorporationOrAllianceInfoJob)->handle($affiliation->affiliatable_type, $affiliation->affiliatable_id));
    }

    public function cleanWaitlist(ControlGroupUpdateData $data)
    {
        $data->role->acl_members()->whereStatus('waitlist')->delete();
    }

    public function removeModerators(ControlGroupUpdateData $data)
    {
        $data->role->moderators()->delete();
    }

    public function removeUnaffiliatedUsers(ControlGroupUpdateData $data)
    {
        $acl_affiliated_ids = $data->role->acl_affiliated_ids;

        $users = User::role($data->role)->whereHas('character_users', function (Builder $query) use ($acl_affiliated_ids) {
            $query->whereNotIn('character_id', $acl_affiliated_ids);
        })->cursor();

        foreach ($users as $user) {
            $data->role->removeMember($user);
        }
    }
}
