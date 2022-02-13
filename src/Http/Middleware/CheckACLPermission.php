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
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

class CheckACLPermission
{
    /**
     * @param  string|null  $character_role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        if (auth()->user()->can('superuser') || auth()->user()->can($permission)) {
            return $next($request);
        }

        $moderated_role_ids = Role::whereHas('moderators', fn ($query) => $query->whereHasMorph(
            'affiliatable',
            [User::class],
            fn ($query) => $query->whereId(auth()->user()->getAuthIdentifier())
        ))->pluck('id');

        $url_parameters = $request->route()->parameters();

        $requested_id = Arr::get($url_parameters, 'role_id');

        if (! $requested_id) {
            abort_unless($moderated_role_ids->isNotEmpty(), 403, 'You do not have the necessary permission to perform this action.');

            return $next($request);
        }

        abort_unless(in_array($requested_id, $moderated_role_ids->toArray()), 403, 'You do not have the necessary permission to perform this action.');

        return $next($request);
    }
}
