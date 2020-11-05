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

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Services\DispatchCorporationOrAllianceInfoJob;

class UpdateOrCreateSsoSettings
{
    /**
     * @var array
     */
    private array $request;

    /**
     * @var \Illuminate\Support\Collection
     */
    private Collection $selected_scopes;

    private Collection $entities;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->selected_scopes = collect(Arr::get($this->request, 'selectedScopes'))
            ->map(fn ($scope) => explode(',', $scope))->flatten(1);
        $this->entities = collect(Arr::get($this->request, 'selectedEntities'));
    }

    public function execute()
    {
        $this->entities->each(function ($entity) {
            $entity_id = Arr::get($entity, 'id');
            $category = Arr::get($entity, 'category');

            $morphable_type = $category === 'corporation' ? CorporationInfo::class : AllianceInfo::class;

            (new DispatchCorporationOrAllianceInfoJob)->handle($morphable_type, $entity_id);

            SsoScopes::updateOrCreate([
                'morphable_id' => $entity_id,
            ], [
                'selected_scopes' => $this->selected_scopes,
                'morphable_type' => $morphable_type,
            ]);
        });
    }
}
