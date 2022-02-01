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

use Closure;
use Illuminate\Support\Arr;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Container\ControlGroupUpdateData;

class OnRequestControlGroupUpdatePipe extends AbstractControlGroupUpdatePipe
{
    public function handle(ControlGroupUpdateData $control_group_update_data, Closure $next)
    {
        if ($control_group_update_data->role_type === 'on-request') {
            $this->update($control_group_update_data);
        }

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
        $moderator_ids = data_get($data, 'moderators.*.id', []);

        // Delete removed affiliations
        $data->role
            ->moderators()
            ->whereNotIn('affiliatable_id', $moderator_ids)
            ->delete();

        // First get the moderators
        collect($data->moderators)
            ->whenNotEmpty(function ($moderators) use ($data) {
                $existing_ids = $data
                    ->role
                    ->moderators
                    ->map(fn ($affiliation) => $affiliation->affiliatable_id)
                    ->toArray();

                $moderators
                    ->reject(fn ($moderator) => in_array($moderator['id'], $existing_ids))
                    ->each(fn ($affiliation) => $data->role->acl_affiliations()->create([
                        'affiliatable_id' => Arr::get($affiliation, 'id'),
                        'affiliatable_type' => User::class,
                        'can_moderate' => true,
                    ]));

                return $moderators;
            })
            ->whenEmpty(fn ($affiliations) => $data->role->moderators()->delete());
    }
}
