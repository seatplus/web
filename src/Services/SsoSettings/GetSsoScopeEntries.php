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

namespace Seatplus\Web\Services\SsoSettings;

use Illuminate\Support\Collection;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;

class GetSsoScopeEntries
{
    public function execute(): Collection
    {
        return SsoScopes::with('morphable')->get()->map(function ($scope) {

            $selectedEntity = [
                'id' => $scope->morphable_id,
                'name' => optional($scope->morphable)->name,
            ];

            $selectedEntity = array_merge($selectedEntity, $scope->morphable_type === CorporationInfo::class
                ? ['corporation_id' => $scope->morphable_id]
                : ['alliance_id' => $scope->morphable_id]
            );

            return [
                'selectedEntity' => $selectedEntity,
                'selectedScopes' => $scope->selected_scopes,
            ];
        });
    }
}
