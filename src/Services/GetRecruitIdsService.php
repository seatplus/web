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
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

class GetRecruitIdsService
{
    private const string PERMISSION = 'can accept or deny applications';

    private const string CORPORATION_ROLE = 'Director';

    private const int CACHE_DURATION_MINUTES = 15;

    private static ?self $instance = null;

    private function __construct(
        private readonly GetAffiliatedIds $affiliatedIdsService
    ) {}

    public static function get(?GetAffiliatedIds $getAffiliatedIds = null): array
    {
        if (self::$instance === null) {
            self::$instance = new self($getAffiliatedIds ?? new GetAffiliatedIds);
        }

        return self::$instance->fetchRecruits();
    }

    /**
     * The recruit scope is keyed and queried straight from its inputs — the affiliated corporation set
     * is never resolved into PHP:
     *  - the cache key is a fingerprint of what the scope is built from (roles, corporation roles,
     *    their affiliation rows, superuser), instead of a hash of the resolved id array;
     *  - the query constrains `corporation_id` with an AffiliationResolver subquery instead of a
     *    whereIn over that array.
     *
     * Both take the user explicitly: the service is a process-lived singleton, so the GetAffiliatedIds
     * it holds may have captured a *different* user (or none) when it was constructed.
     */
    private function fetchRecruits(): array
    {
        $user = auth()->user();

        $cacheKey = 'recruit_character_ids_'.$this->affiliatedIdsService->affiliatedCorporationScopeFingerprint(
            self::PERMISSION,
            self::CORPORATION_ROLE,
            $user,
        );

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(self::CACHE_DURATION_MINUTES),
            fn () => $this->queryRecruits($user)
        );
    }

    private function queryRecruits(?User $user): array
    {
        return Application::query()
            ->with([
                'applicationable' => fn (MorphTo $morphTo) => $morphTo->morphWith([
                    User::class => ['characters'],
                ]),
            ])
            // Superuser bypass included, so this replaces the former when(! superuser) wrapper.
            ->tap(fn (Builder $query) => $this->affiliatedIdsService->constrainToAffiliated(
                query: $query,
                column: 'corporation_id',
                permissions: self::PERMISSION,
                corporationRoles: self::CORPORATION_ROLE,
                user: $user,
            ))
            ->where('status', 'open')
            ->get()
            ->map(function (Application $recruit) {
                $applicationable = $recruit->applicationable;

                if ($applicationable instanceof User) {
                    return $applicationable->characters->pluck('character_id');
                }

                if ($applicationable instanceof CharacterInfo) {
                    return collect([$applicationable->character_id]);
                }

                return collect();
            })
            ->flatten()
            ->unique()
            ->map(fn (int|string $recruitId) => intval($recruitId))
            ->filter()
            ->toArray();
    }
}
