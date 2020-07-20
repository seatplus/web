<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
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
            ->whereDoesntHave('roles', fn ($query) => $query->whereId($control_group_update_data->role->id))
            ->cursor();

        foreach ($users as $user) {
            $control_group_update_data->role->activateMember($user);
        }
    }
}
