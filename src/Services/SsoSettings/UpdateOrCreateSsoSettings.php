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
use Seatplus\Eveapi\Actions\Jobs\Alliance\AllianceInfoAction;
use Seatplus\Eveapi\Actions\Jobs\Corporation\CorporationInfoAction;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;

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

    private array $entity;

    private int $entity_id;

    public function __construct(array $request)
    {

        $this->request = $request;
        $this->selected_scopes = collect(Arr::get($this->request, 'selectedScopes')); //collect($this->request->input('selectedScopes'))->toJson();
        $this->entity = Arr::get($this->request, 'selectedCorpOrAlliance');
        $this->entity_id = Arr::get($this->entity, 'id');
    }

    public function execute()
    {
        $morphable_type = Arr::has($this->entity, 'corporation_id') ? CorporationInfo::class : AllianceInfo::class;

        $this->dispatchInfoJob($morphable_type);

        SsoScopes::updateOrCreate([
            'morphable_id' => $this->entity_id,
            'selected_scopes' => $this->selected_scopes,
        ], [
            'morphable_type' => $morphable_type,
        ]);
    }

    private function dispatchInfoJob(string $morphable_type)
    {
        $morphable_type === AllianceInfo::class ? $this->handleAllianceInfo() : $this->handleCorporationInfo();
    }

    private function handleAllianceInfo()
    {

        (new AllianceInfoAction)->execute($this->entity_id);

    }

    private function handleCorporationInfo()
    {
        (new CorporationInfoAction)->execute($this->entity_id);
    }
}
