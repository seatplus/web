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
use Illuminate\Support\Arr;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Container\ControlGroupUpdateData;

class OnRequestControlGroupUpdatePipe extends AbstractControlGroupUpdatePipe
{
    public function handle(ControlGroupUpdateData $control_group_update_data, Closure $next)
    {
        if($control_group_update_data->role_type === 'on-request')
            $this->update($control_group_update_data);

        return $next($control_group_update_data);
    }

    private function update(ControlGroupUpdateData $control_group_update_data)
    {
        $this->handleAffiliations($control_group_update_data);
        $this->handleModerators($control_group_update_data);
        $this->removeUnaffiliatedUsers($control_group_update_data);
    }

    private function handleModerators(ControlGroupUpdateData $data)
    {

        // First get the moderators
        collect($data->affiliations)
            ->whenNotEmpty(function ($affiliations) use ($data) {

                // Delete removed moderators
                $affiliatable_ids = $affiliations
                    ->filter(fn ($affiliation) => Arr::has($affiliation, 'affiliatable_id'))
                    // Only keep moderators
                    ->filter(fn ($affiliation) => Arr::has($affiliation, 'can_moderate') ? Arr::get($affiliation, 'can_moderate') : false)
                    ->map(fn ($affiliation) => $affiliation['affiliatable_id']);

                $data->role
                    ->moderators()
                    ->whereNotIn('affiliatable_id', $affiliatable_ids)
                    ->delete();

                return $affiliations;
            })
            ->whenNotEmpty(function ($affiliations) use ($data) {

                // add affiliations
                $affiliations
                    ->filter(fn ($affiliation) => Arr::has($affiliation, 'user_id'))
                    ->filter(fn ($affiliation) => Arr::has($affiliation, 'can_moderate') ? Arr::get($affiliation, 'can_moderate') : false)
                    ->each(fn ($affiliation) => $data->role->acl_affiliations()->create([
                        'affiliatable_id'   => Arr::get($affiliation, 'user_id'),
                        'affiliatable_type' => User::class,
                        'can_moderate'      => true,
                    ]));

                return $affiliations;
            })
            ->whenEmpty(fn ($affiliations) => $data->role->moderators()->delete());
    }
}
