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

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Arr;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Web\Container\ControlGroupUpdateData;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Controllers\Request\ControlGroupUpdate;
use Seatplus\Web\Services\Pipes\AutomaticControlGroupUpdatePipe;
use Seatplus\Web\Services\Pipes\ManualControlGroupControlGroupUpdatePipe;
use Seatplus\Web\Services\Pipes\OnRequestControlGroupUpdatePipe;
use Seatplus\Web\Services\Pipes\OptInControlGroupUpdatePipe;

class UpdateControlGroupController extends Controller
{
    private array $pipes = [
        ManualControlGroupControlGroupUpdatePipe::class,
        AutomaticControlGroupUpdatePipe::class,
        OptInControlGroupUpdatePipe::class,
        OnRequestControlGroupUpdatePipe::class,
    ];

    public function __invoke(ControlGroupUpdate $control_group_update, int $role_id)
    {
        $control_group_update_data = new ControlGroupUpdateData([
            'role' => Role::findById($role_id),
            'role_type' => Arr::get($control_group_update, 'acl.type'),
            'affiliations' => Arr::get($control_group_update, 'acl.affiliations'),
            'members' => Arr::get($control_group_update, 'acl.members'),
            'moderators' => Arr::get($control_group_update, 'acl.moderators', []),
        ]);

        $this->updateType($control_group_update_data);

        app(Pipeline::class)
            ->send($control_group_update_data)
            ->through($this->pipes)
            ->then(fn () => logger()->info('Control group updated'));

        return redirect()->route('acl.manage', $role_id)->with('success', 'updated');
    }

    private function updateType(ControlGroupUpdateData $data): void
    {
        if ($data->role->type === $data->role_type) {
            return;
        }

        $role = $data->role;
        $role->type = $data->role_type;
        $role->save();
    }
}
