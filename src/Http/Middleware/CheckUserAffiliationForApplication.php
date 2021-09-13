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
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;

class CheckUserAffiliationForApplication
{
    /**
     * @param  Closure  $next
     * @param $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = $request->recruit;

        abort_unless($user, 404, 'required url parameter user_id is missing');

        $affiliated_ids = getAffiliatedIdsByPermission($permission);

        $application = Application::whereIn('corporation_id', $affiliated_ids)
            ->where(['applicationable_type' => User::class, 'applicationable_id' => $user->id])
            ->with('applicationable')
            ->first();

        abort_unless($application, 403, 'You are not allowed to review the user');

        return $next($request);
    }
}
