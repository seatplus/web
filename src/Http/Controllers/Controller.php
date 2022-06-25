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
use Illuminate\Routing\Controller as BaseController;
use Seatplus\Auth\Services\Affiliations\GetAffiliatedIdsService;
use Seatplus\Auth\Services\Affiliations\GetOwnedAffiliatedIdsService;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;
use Seatplus\Web\Services\GetRecruitIdsService;

class Controller extends BaseController
{
    use ValidatesRequests;

    protected function joinAffiliated(Builder $query, string $table, string $column, object $dispatchTransferObject, bool $ownedOnly = false)
    {
        $affiliations_dto = new AffiliationsDto(
            permissions: [data_get($dispatchTransferObject, 'permission')],
            user: auth()->user(),
            corporation_roles: data_get($dispatchTransferObject, 'corporation_roles'),
        );

        $owned_query = GetOwnedAffiliatedIdsService::make($affiliations_dto)->getQuery();
        $affiliated_query = GetAffiliatedIdsService::make($affiliations_dto)->getQuery();

        $subquery = $ownedOnly ? $owned_query : $owned_query->union($affiliated_query);

        $query->joinSub(
            $subquery,
            'affiliations',
            'affiliations.affiliated_id', '=', "$table.$column"
        );

        return $ownedOnly? $query : $query->orWhereIn("$column", GetRecruitIdsService::get());
    }
}
