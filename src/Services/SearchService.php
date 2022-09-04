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

namespace Seatplus\Web\Services;

use phpDocumentor\Reflection\Types\This;
use Seatplus\Eveapi\Models\Alliance\AllianceInfo;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Constellation;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Region;
use Seatplus\Eveapi\Models\Universe\System;
use Seatplus\Eveapi\Models\Universe\Type;

class SearchService
{

    private bool $isIdsOnly = true;

    /**
     * @return bool
     */
    public function isIdsOnly(): bool
    {
        return $this->isIdsOnly;
    }


    public function setIsIdsOnly(bool $isIdsOnly): SearchService
    {
        $this->isIdsOnly = $isIdsOnly;

        return $this;
    }

    public function execute(string|array $categories, string $query): array
    {

        $result = collect($categories)
            ->mapWithKeys(fn($category) => [$category => $this->getSearchResult($category, $query)]);

        // TODO don't forget to add hasEveImage

        return $result->toArray();
    }

    private function getSearchResult(string $category, string $term) : array
    {
        $query = match ($category) {
            // Items
            'type' => Type::query()->select(['type_id as id', 'name']),
            'group' => Group::query()->select(['group_id as id', 'name']),
            'category' => Category::query()->select(['category_id as id', 'name']),
            // Locations
            'solar_system' => System::query()->select(['system_id as id', 'name']),
            'region' => Region::query()->select(['region_id as id', 'name']),
            'constellation' => Constellation::query()->select(['constellation_id as id', 'name']),
            // Entity
            'character' => CharacterInfo::query()->select(['character_id as id', 'name']),
            'corporation' => CorporationInfo::query()->select(['corporation_id as id', 'name']),
            'alliance' => AllianceInfo::query()->select(['alliance_id as id', 'name']),
        };

        match ($category) {
            'type', 'group' => $query->where('name_normalized', 'like', $term . '%'),
            default => $query->where('name', 'like', $term . '%'),
        };

        return $this->isIdsOnly()
            ? $query-> pluck('id')->toArray()
            : $query->limit(15)->get()->map(fn($result) => [
                'id' => $result->id,
                'name' => $result->name,
                'category' => $category
            ])->toArray();
    }


}
