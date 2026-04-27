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

namespace Seatplus\Web\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Services\Controller\DispatchTransferObject;
use Seatplus\Web\Services\GetAffiliatedIds;

class Controller extends BaseController
{
    use ValidatesRequests;

    private const CHARACTER_IDS_FILTER = 'character_ids';

    public function __construct(
        protected readonly Request $request,
        protected readonly GetAffiliatedIds $getAffiliatedIds
    ) {}

    protected function getCharacterIds(
        DispatchTransferObject $dispatchTransferObject,
        ?string $characterRelation = null
    ): Collection {
        $affiliatedIds = $this->getAffiliatedIds($dispatchTransferObject);
        $filteredIds = $this->filterByRequestedCharacterIds($affiliatedIds);

        return $this->fetchAffiliatedCharacterIdsWithRelation($filteredIds, $characterRelation);
    }

    protected function fetchAffiliatedCharacterIdsWithRelation(
        array $characterIds,
        ?string $characterRelation = null
    ): \Illuminate\Database\Eloquent\Collection {
        return CharacterInfo::query()
            ->select('character_id')
            ->whereIn('character_id', $characterIds)
            ->when(
                $characterRelation,
                fn (Builder $query) => $query->with($characterRelation),
            )
            ->get();
    }

    protected function getAffiliatedIds(DispatchTransferObject $dispatchTransferObject): array
    {
        return $this->getAffiliatedIds->get(
            $dispatchTransferObject->permission,
            $dispatchTransferObject->required_corporation_role
        );
    }

    protected function getOwnedCharacterIds(): array
    {
        return auth()->user()->characters->pluck('character_id')->toArray();
    }

    private function filterByRequestedCharacterIds(array $affiliatedIds): array
    {
        if ($this->request->has(self::CHARACTER_IDS_FILTER)) {
            return array_intersect(
                $affiliatedIds,
                $this->request->get(self::CHARACTER_IDS_FILTER)
            );
        }

        return $affiliatedIds;
    }
}
