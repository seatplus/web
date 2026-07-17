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

namespace Seatplus\Web\Models\Recruitment;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Recruitment\Enlistments;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Region;
use Seatplus\Eveapi\Models\Universe\System;
use Seatplus\Eveapi\Models\Universe\Type;

class Enlistment extends Enlistments
{
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'corporation_id', 'corporation_id');
    }

    /**
     * Eager-load the given user's open applications onto each enlistment. Replaces the
     * former `list.existing.applications` JSON endpoint the dashboard used to poll per card.
     */
    #[Scope]
    protected function withOpenApplicationsOf(Builder $query, User $user): void
    {
        $query->with(['applications' => function (HasMany $query) use ($user) {
            $query->where('status', 'open')
                ->whereHasMorph(
                    'applicationable',
                    [User::class, CharacterInfo::class],
                    function (Builder $query, string $type) use ($user) {
                        match ($type) {
                            User::class => $query->whereKey($user->getAuthIdentifier()),
                            CharacterInfo::class => $query->whereIn('character_id', $user->characters()->pluck('character_infos.character_id')),
                            default => null,
                        };
                    }
                );
        }]);
    }

    public function systems(): MorphToMany
    {
        return $this->morphedByMany(System::class, 'watchlistable', null, 'corporation_id');
    }

    public function regions(): MorphToMany
    {
        return $this->morphedByMany(Region::class, 'watchlistable', null, 'corporation_id');
    }

    public function types(): MorphToMany
    {
        return $this->morphedByMany(Type::class, 'watchlistable', null, 'corporation_id');
    }

    public function groups(): MorphToMany
    {
        return $this->morphedByMany(Group::class, 'watchlistable', null, 'corporation_id');
    }

    public function categories(): MorphToMany
    {
        return $this->morphedByMany(Category::class, 'watchlistable', null, 'corporation_id');
    }
}
