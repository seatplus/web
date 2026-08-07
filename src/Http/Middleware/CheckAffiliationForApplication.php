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
use Seatplus\Web\Services\GetAffiliatedIds;
use Symfony\Component\HttpFoundation\Response;

/**
 * The gate on every route that exposes an applicant to a recruiter. Two things must hold: the recruiter
 * is affiliated with the corporation applied to, and the application is still open.
 *
 * The status filter is a retention commitment, not an incidental check. Granting ESI data to a
 * recruitment process is scoped to *that process*: the moment an application is accepted or rejected the
 * recruiter's access to the applicant's character data ends. There is no grace period — a decided
 * applicant is not inspectable, and the reviews History list therefore links to the decision record
 * only, never back into the inspection tabs.
 *
 * This middleware carries that alone. CharacterInspectionScrollProps builds the assets/wallet/mail/
 * contacts/contracts props straight from character ids with no recruit check of its own, so nothing
 * downstream re-checks what is allowed here.
 *
 * Multi-stage review is unaffected: ReviewApplicationAction only writes a terminal status on rejection
 * or on acceptance at the final round, so an application mid-pipeline stays open and stays reachable.
 */
class CheckAffiliationForApplication
{
    private const string ACTIVE_STATUS = 'open';

    public function __construct(
        private GetAffiliatedIds $getAffiliatedIdsService,
    ) {}

    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if ($request->user()->can('superuser')) {
            return $next($request);
        }

        $application_id = $request->application_id;

        abort_unless($application_id, 404, 'required url parameter application_id is missing');

        $query = Application::query()
            ->where('id', $application_id)
            ->where('status', self::ACTIVE_STATUS)
            ->with(['applicationable', 'corporation']);

        $this->getAffiliatedIdsService->constrainToAffiliated(
            query: $query,
            column: 'corporation_id',
            permissions: [$permission],
            corporationRoles: ['director'],
            user: auth()->user(),
        );

        $application = $query->exists();

        // One message for both misses (wrong corporation / already decided) — distinguishing them would
        // tell a caller whether an application id exists and how it ended.
        abort_unless($application, 403, 'You are not allowed to review the recruit');

        return $next($request);
    }
}
