<?php


namespace Seatplus\Web\Services\Pipes;


use Closure;
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
    }
}
