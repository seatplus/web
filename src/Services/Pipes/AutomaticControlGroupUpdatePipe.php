<?php


namespace Seatplus\Web\Services\Pipes;


use Closure;
use Illuminate\Database\Eloquent\Builder;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Container\ControlGroupUpdateData;

class AutomaticControlGroupUpdatePipe extends AbstractControlGroupUpdatePipe
{

    public function handle(ControlGroupUpdateData $control_group_update_data, Closure $next)
    {
        if($control_group_update_data->role_type === 'automatic')
            $this->update($control_group_update_data);

        return $next($control_group_update_data);
    }

    private function update(ControlGroupUpdateData $control_group_update_data)
    {
        $this->handleAffiliations($control_group_update_data);

        $this->addAffiliatedUsers($control_group_update_data);
        $this->removeUnaffiliatedUsers($control_group_update_data);

        $this->cleanWaitlist($control_group_update_data);
        $this->removeModerators($control_group_update_data);
    }

    private function addAffiliatedUsers(ControlGroupUpdateData $control_group_update_data)
    {
        $acl_affiliated_ids = $control_group_update_data->role->acl_affiliated_ids;

        $users = User::query()
            ->whereHas('character_users', function (Builder $query) use ($acl_affiliated_ids) {
                $query->whereIn('character_id', $acl_affiliated_ids);
            })
            ->whereDoesntHave('roles', fn($query) => $query->whereId($control_group_update_data->role->id))
            ->cursor();

        foreach ($users as $user) {
            $control_group_update_data->role->activateMember($user);
        }
    }
}
