<?php


namespace Seatplus\Web\Services\Pipes;


use Closure;
use Seatplus\Web\Container\ControlGroupUpdateData;

class ManualControlGroupControlGroupUpdatePipe extends AbstractControlGroupUpdatePipe
{

    public function handle(ControlGroupUpdateData $control_group_update_data, Closure $next)
    {

        if($control_group_update_data->role_type === 'manual')
            $this->update($control_group_update_data);

        return $next($control_group_update_data);
    }

    private function update(ControlGroupUpdateData $data)
    {
        $this->handleMember($data);

        $this->cleanWaitlist($data);
        $this->removeModerators($data);
    }


}
