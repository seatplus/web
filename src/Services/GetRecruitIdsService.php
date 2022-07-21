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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Affiliations\GetAffiliatedIdsService;
use Seatplus\Auth\Services\Affiliations\GetOwnedAffiliatedIdsService;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;
use Seatplus\Eveapi\Models\Application;

class GetRecruitIdsService
{
    public static function get(): array
    {
        $permission = 'can accept or deny applications';

        $affiliations_dto = new AffiliationsDto(
            permissions: [$permission],
            user: auth()->user(),
            corporation_roles: ['Director']
        );

        $cache_key = hash('sha256', json_encode($affiliations_dto));

        return Cache::remember($cache_key, now()->addMinutes(15), function () use ($affiliations_dto) {
            $affiliated_ids = GetAffiliatedIdsService::make($affiliations_dto)->getQuery();
            $owned_ids = GetOwnedAffiliatedIdsService::make($affiliations_dto)->getQuery();

            $affiliated = $affiliated_ids->union($owned_ids);

            return Application::query()
                ->with([
                    'applicationable' => fn (MorphTo $morph_to) => $morph_to->morphWith([
                        User::class => ['characters'],
                    ]),
                ])
                ->when(
                    ! $affiliations_dto->user->can('superuser'),
                    fn (Builder $query) => $query
                    ->joinSub($affiliated, 'affiliated', 'applications.corporation_id', '=', 'affiliated.affiliated_id')
                )
                ->where('status', 'open')
                ->get()
                ->map(
                    fn ($recruit) => $recruit->applicationable->characters
                        ? $recruit->applicationable->characters->pluck('character_id')
                        : $recruit->applicationable->character_id
                )
                ->flatten()
                ->unique()
                ->map(fn ($recruit_id) => intval($recruit_id))
                ->filter()
                ->toArray();
        });
    }
}
