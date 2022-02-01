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

namespace Seatplus\Web\Services\Controller;

use Illuminate\Support\Collection;
use Seatplus\Web\Services\GetRecruitIdsService;

class GetAffiliatedIdsService
{
    private string $request_parameter;

    private string $required_corporation_role = '';

    private string $permission;

    private function getPermission(): string
    {
        return $this->permission;
    }

    public function setPermission(string $permission): GetAffiliatedIdsService
    {
        $this->permission = $permission;

        return $this;
    }

    private function getRequiredCorporationRole(): string
    {
        return $this->required_corporation_role;
    }

    public function setRequiredCorporationRole(string $required_corporation_role): GetAffiliatedIdsService
    {
        $this->required_corporation_role = $required_corporation_role;

        return $this;
    }

    private function getRequestParameter(): string
    {
        return $this->request_parameter;
    }

    public function setRequestFlavour(string $flavour): GetAffiliatedIdsService
    {
        $this->request_parameter = match ($flavour) {
            'character' => 'character_ids',
            'corporation' => 'corporation_ids'
        };

        return $this;
    }

    public static function make()
    {
        return new static();
    }

    public function get(): Collection
    {
        $ids = request()->has($this->getRequestParameter())
            ? request()->get($this->getRequestParameter())
            : $this->getOwnedIds();

        return collect($ids)
            ->intersect([...$this->getAffiliatedIds(), ...GetRecruitIdsService::get()])
            ->map(fn ($id) => (int) $id);
    }

    public function viaDispatchTransferObject(object $dispatchTransferObject): GetAffiliatedIdsService
    {
        $this->permission = data_get($dispatchTransferObject, 'permission');
        $this->required_corporation_role = data_get($dispatchTransferObject, 'required_corporation_role');

        return $this;
    }

    private function getOwnedIds(): array
    {
        return match ($this->getRequestParameter()) {
            'character_ids' => auth()->user()->characters->pluck('character_id')->toArray(),
            'corporation_ids' => auth()->user()->characters->map(fn ($character) => $character->corporation->corporation_id)->toArray()
        };
    }

    private function getAffiliatedIds(): array
    {
        return getAffiliatedIdsByPermission($this->getPermission(), $this->getRequiredCorporationRole());
    }
}
