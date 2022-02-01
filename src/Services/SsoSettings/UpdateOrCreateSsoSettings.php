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

namespace Seatplus\Web\Services\SsoSettings;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Web\Services\DispatchCorporationOrAllianceInfoJob;

class UpdateOrCreateSsoSettings
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private Collection $selected_scopes;

    private Collection $entities;

    private string $type;

    public function __construct(
        private array $request
    ) {
        $this->buildSelectedScopes();

        $this->entities = collect(Arr::get($this->request, 'selectedEntities'));
        $this->type = Arr::get($this->request, 'type');
    }

    public function execute()
    {
        $this->entities->whenEmpty(
            function () {
                if ($this->type === 'global') {
                    SsoScopes::updateOrCreate(['type' => 'global'], ['selected_scopes' => $this->selected_scopes]);
                }
            },
            fn ($collection) => $collection
            ->each(function ($entity) {
                $entity_id = Arr::get($entity, 'id');
                $category = Arr::get($entity, 'type');

                $morphable_type = match ($category) {
                    'corporation' => CorporationInfo::class,
                    'alliance' => AllianceInfo::class,
                };

                (new DispatchCorporationOrAllianceInfoJob())->handle($morphable_type, $entity_id);

                SsoScopes::updateOrCreate([
                    'morphable_id' => $entity_id,
                ], [
                    'selected_scopes' => $this->selected_scopes->unique(),
                    'morphable_type' => $morphable_type,
                    'type' => $this->type,
                ]);
            })
        );
    }

    private function buildSelectedScopes(): void
    {
        $this->selected_scopes = collect();

        collect(Arr::get($this->request, 'selectedScopes'))
            ->flatMap(fn ($scope) => explode(',', $scope))
            ->each(function ($scope) {

                // If it is a corporation scope, we need to know the characters role
                if (Str::of($scope)->contains('corporation')) {
                    $this->selected_scopes->push('esi-characters.read_corporation_roles.v1');
                }

                $this->selected_scopes->push($scope);
            });

        if (Arr::hasAny($this->selected_scopes->unique()->toArray(), ['esi-wallet.read_corporation_wallets.v1'])) {
            $this->selected_scopes->push('esi-corporations.read_divisions.v1');
        }
    }
}
