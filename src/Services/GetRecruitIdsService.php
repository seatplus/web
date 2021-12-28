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

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;

class GetRecruitIdsService
{
    public static function get(): array
    {
        $recruiter_permission = 'can accept or deny applications';

        if (! auth()->user()->can($recruiter_permission)) {
            return [];
        }

        $user_id = auth()->user()->getAuthIdentifier();
        $cache_key = "${recruiter_permission}:${user_id}";

        $recruit_ids = cache($cache_key);

        if(!$recruit_ids) {
            $recruit_ids =  Application::whereIn('corporation_id', getAffiliatedIdsByPermission($recruiter_permission))
                ->whereStatus('open')
                ->with([
                    'applicationable' => fn (MorphTo $morph_to) => $morph_to->morphWith([
                        User::class => ['characters'],
                    ]),
                ])
                ->get()
                ->map(fn ($recruit) => $recruit->applicationable->characters
                    ? $recruit->applicationable->characters->pluck('character_id')
                    : $recruit->applicationable->character_id
                )
                ->flatten()
                ->unique()
                ->toArray();

            cache([$cache_key => $recruit_ids], now()->addMinutes(15));
        }

        return $recruit_ids;
    }
}
