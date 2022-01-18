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
use Seatplus\Eveapi\Models\Application;

class CheckAffiliationForApplication
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $application_id = $request->application_id;

        abort_unless($application_id, 404, 'required url parameter application_id is missing');

        $affiliated_ids = getAffiliatedIdsByPermission($permission);

        $application = Application::query()
            ->whereIn('corporation_id', $affiliated_ids)
            ->where('id', $application_id)
            ->with(['applicationable', 'corporation'])
            ->exists();

        abort_unless($application, 403, 'You are not allowed to review the recruit');

        return $next($request);
    }
}
