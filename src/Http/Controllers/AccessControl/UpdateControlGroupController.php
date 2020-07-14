<?php


namespace Seatplus\Web\Http\Controllers\AccessControl;


use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\ControlGroupUpdate;
use Seatplus\Web\Container\ControlGroupUpdateData;
use Seatplus\Web\Services\Pipes\AutomaticControlGroupUpdatePipe;
use Seatplus\Web\Services\Pipes\ManualControlGroupControlGroupUpdatePipe;
use Illuminate\Pipeline\Pipeline;

class UpdateControlGroupController extends Controller
{
    private array $pipes = [
        ManualControlGroupControlGroupUpdatePipe::class,
        AutomaticControlGroupUpdatePipe::class
    ];

    public function __invoke(ControlGroupUpdate $control_group_update, int $role_id)
    {
        $control_group_update_data = new ControlGroupUpdateData([
            'role' => Role::findById($role_id),
            'role_type' => $control_group_update->type,
            'affiliations' => $control_group_update->affiliations,
            'members' => $control_group_update->members
        ]);

        $this->updateType($control_group_update_data);

        app(Pipeline::class)
            ->send($control_group_update_data)
            ->through($this->pipes)
            ->then(fn () => logger()->info('Control group updated'));

    }

    private function updateType(ControlGroupUpdateData $data) : void
    {

        if($data->role->type === $data->role_type)
            return;

        $role = $data->role;
        $role->type = $data->role_type;
        $role->save();
    }

}
