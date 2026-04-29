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
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\DTO\CriteriaData;
use Seatplus\Auth\Services\Roles\OnRequestRoleService;
use Seatplus\Web\Container\ControlGroupUpdateData;

class OnRequestControlGroupUpdatePipe extends AbstractControlGroupUpdatePipe
{
    public function handle(ControlGroupUpdateData $control_group_update_data, Closure $next): ControlGroupUpdateData
    {
        if ($control_group_update_data->role_type === 'on-request') {
            $this->update($control_group_update_data);
        }

        return $next($control_group_update_data);
    }

    private function update(ControlGroupUpdateData $data): void
    {
        $service = new OnRequestRoleService($data->role);

        $this->handleAffiliations($data);

        $criteria = collect($data->affiliations ?? [])
            ->map(fn (array $affiliation) => new CriteriaData(
                entity_id: (int) $affiliation['id'],
                entity_type: $affiliation['category'],
            ))
            ->all();

        $service->addCriteriaForRoleApplication(...$criteria);

        $service->handleMembers();

        $this->handleModerators($data, $service);
    }

    private function handleModerators(ControlGroupUpdateData $data, OnRequestRoleService $service): void
    {
        // Clear all current moderators first
        $this->removeModerators($data);

        // Re-assign moderators from request
        collect($data->moderators ?? [])
            ->each(fn (array $moderator) => $service->setModerator(User::findOrFail((int) $moderator['id'])));
    }
}
