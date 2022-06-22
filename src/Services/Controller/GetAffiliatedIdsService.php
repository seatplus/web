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

use Illuminate\Contracts\Database\Eloquent\Builder;
use Seatplus\Auth\Services\Affiliations\GetOwnedAffiliatedIdsService;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

class GetAffiliatedIdsService
{

    private AffiliationsDto $affiliations_dto;

    public function __construct(object $dispatchTransferObject)
    {
        $this->affiliations_dto = new AffiliationsDto(
            permissions: [data_get($dispatchTransferObject, 'permission')],
            user: auth()->user(),
            corporation_roles: data_get($dispatchTransferObject, 'required_corporation_role')
        );
    }

    public static function make(object $dispatchTransferObject)
    {
        return new static($dispatchTransferObject);
    }

    private function getOwnedAffiliatedIdsQuery() : Builder
    {
        return GetOwnedAffiliatedIdsService::make($this->getAffiliationsDto())->getQuery();
    }

    public function getCharacterIds(): array
    {

        return CharacterInfo::query()
            ->joinSub(
                $this->getOwnedAffiliatedIdsQuery(),
                'owned',
                'owned.affiliated_id', '=', 'character_infos.character_id'
            )
            ->pluck('character_id')
            ->values()
            ->toArray();
    }

    /**
     * @return AffiliationsDto
     */
    public function getAffiliationsDto(): AffiliationsDto
    {
        return $this->affiliations_dto;
    }

}
