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

namespace Seatplus\Web\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;

class CheckPermissionAffiliation
{
    /**
     * @param Closure $next
     * @param $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (auth()->user()->can('superuser')) {
            return $next($request);
        }

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        $url_parameters = $request->route()->parameters();

        $requested_id = collect([
            Arr::get($url_parameters, 'character_id'),
            Arr::get($url_parameters, 'corporation_id'),
            Arr::get($url_parameters, 'alliance_id'),
        ])->filter();

        if ($requested_id->isEmpty()) {
            abort_unless($this->checkUserPermissions($permissions), 403, 'You do not have the necessary permission to perform this action.');

            return $next($request);
        }

        $affiliated_ids = $this->buildAffiliatedIdsByPermissions($permissions);

        $recruit_ids = $this->buildRecruitIds();

        abort_unless($requested_id->intersect([...$affiliated_ids, ...$recruit_ids])->isNotEmpty(), 403, 'You are not allowed to access the requested entity');

        return $next($request);
    }

    private function checkUserPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (auth()->user()->can($permission)) {
                return true;
            }
        }

        return false;
    }

    private function buildAffiliatedIdsByPermissions(array $permissions): array
    {
        $ids = collect();

        foreach ($permissions as $permission) {
            $affiliatedIds = getAffiliatedIdsByPermission($permission);

            $ids->push($affiliatedIds);
        }

        return $ids->flatten()->unique()->toArray();
    }

    private function buildRecruitIds(): array
    {
        $recruiter_permission = 'can accept or deny applications';

        if (! auth()->user()->can($recruiter_permission)) {
            return [];
        }

        return Application::whereIn('corporation_id', $this->buildAffiliatedIdsByPermissions([$recruiter_permission]))
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
            ->toArray();
    }
}
