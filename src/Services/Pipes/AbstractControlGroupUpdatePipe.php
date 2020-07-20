<?php


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
            ->each(fn($member) => $data->role->removeMember(User::find($member['user_id'])));

        // get missing members
        $current_member_ids = $data->role->fresh()->members()->pluck('user_id');

        collect($data->members)
            ->reject(fn($member) => in_array($member['user_id'], $current_member_ids->toArray()))
            // remove members on waitlist or paused
            ->reject(fn($member) => Arr::has($member, 'status') ? $member['status'] !== 'member' : false)
            ->each(fn($member) => $data->role->activateMember(User::find($member['user_id'])));
    }

    public function handleAffiliations(ControlGroupUpdateData $data)
    {
        // First get the affiliations to delete
        collect($data->affiliations)
            ->whenNotEmpty(function ($affiliations) use ($data) {

                // Delete removed affiliations
                $affiliatable_ids = $affiliations
                    ->filter(fn($affiliation) => Arr::has($affiliation, 'affiliatable_id'))
                    ->map(fn($affiliation) => $affiliation['affiliatable_id']);

                $data->role
                    ->acl_affiliations()
                    ->whereNotIn('affiliatable_id', $affiliatable_ids)
                    ->delete();

                return $affiliations;
            })
            ->whenNotEmpty(function ($affiliations) use ($data) {

                // add affiliations
                $affiliations
                    ->filter(fn($affiliation) => Arr::has($affiliation, 'id'))
                    ->each(fn($affiliation) => $data->role->acl_affiliations()->create([
                        'affiliatable_id' => $affiliation['id'],
                        'affiliatable_type' => $affiliation['category'] === 'corporation' ? CorporationInfo::class : AllianceInfo::class
                    ]));

                return $affiliations;
            })
            ->whenEmpty(fn($affiliations) => $data->role->acl_affiliations()->delete());

        $data->role->refresh()
            ->acl_affiliations()
            ->whereDoesntHaveMorph('affiliatable',
                [CorporationInfo::class, AllianceInfo::class]
            )
            ->get()
            ->each(fn($affiliation) => (new DispatchCorporationOrAllianceInfoJob)->handle($affiliation->affiliatable_type, $affiliation->affiliatable_id));

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
