<?php


namespace Seatplus\Web\Services\Pipes;


use Closure;
use Seatplus\Web\Container\ControlGroupUpdateData;

interface ControlGroupUpdatePipe
{
    public function handle(ControlGroupUpdateData $control_group_update_data, Closure $next);
}
