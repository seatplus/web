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

namespace Seatplus\Web\Http\Controllers\Corporation\Recruitment;

use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ScrollProp;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Recruitment\Enlistments;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\ApplicationRessource;

class GetRecruitmentIndexController extends Controller
{
    final public const string MANAGEPERMISSION = 'can open or close corporations for recruitment';

    final public const string RECRUITERPERMISSION = 'can accept or deny applications';

    public function __invoke(): Response
    {
        $can_manage_recruitment = auth()->user()->can(self::MANAGEPERMISSION);

        $enlistments = $this->getEnlistments();

        return Inertia::render('Corporation/Recruitment/RecruitmentIndex', [
            'canManageRecruitment' => $can_manage_recruitment,
            'enlistments' => $enlistments,
            'activeSidebarElement' => 'corporation.recruitment',
            // One infinite-scroll prop per (corporation × review step) for open applications
            // plus one per corporation for closed applications. Each paginator uses its own
            // pageName so their scroll state never collides; the frontend <InfiniteScroll>
            // reads it via the matching `open_{corporation}_{step}` / `closed_{corporation}`
            // key. Scroll props are deferred, so only the table the recruiter is looking at
            // actually resolves — replacing the per-list axios endpoints.
            ...$this->applicationScrollProps($enlistments),
        ]);
    }

    /**
     * Build the per-enlistment infinite-scroll props for open (per review step) and
     * closed applications.
     *
     * @return array<string, ScrollProp>
     */
    private function applicationScrollProps(Collection $enlistments): array
    {
        $props = [];

        foreach ($enlistments as $enlistment) {
            $corporationId = $enlistment->corporation_id;

            foreach (range(0, max($enlistment->steps_count - 1, 0)) as $decisionCount) {
                $key = "open_{$corporationId}_{$decisionCount}";

                $props[$key] = Inertia::scroll(
                    fn () => ApplicationRessource::collection(
                        $this->openApplicationsQuery($corporationId, $decisionCount)->paginate(pageName: $key)
                    ),
                );
            }

            $closedKey = "closed_{$corporationId}";

            $props[$closedKey] = Inertia::scroll(
                fn () => ApplicationRessource::collection(
                    $this->closedApplicationsQuery($corporationId)->paginate(pageName: $closedKey)
                ),
            );
        }

        return $props;
    }

    private function openApplicationsQuery(int $corporationId, int $decisionCount): Builder
    {
        return Application::query()
            ->with('logEntries')
            ->whereHas('logEntries', function (Builder $query) {
                $query->where('type', 'decision');
            }, '=', $decisionCount)
            ->ofCorporation($corporationId)
            ->whereStatus('open');
    }

    private function closedApplicationsQuery(int $corporationId): Builder
    {
        return Application::query()
            ->ofCorporation($corporationId)
            ->latest('updated_at')
            ->where('status', '<>', 'open');
    }

    private function getEnlistments(): Collection
    {

        $isSuperuser = auth()->user()->can('superuser');

        $manageableIds = $this->getAffiliatedIds->get(
            permissions: [self::MANAGEPERMISSION],
            corporationRoles: ['Director'],
            user: auth()->user()
        );

        $recruiterIds = $this->getAffiliatedIds->get(
            permissions: [self::RECRUITERPERMISSION],
            corporationRoles: ['Director'],
            user: auth()->user()
        );

        return DB::transaction(function () use ($isSuperuser, $manageableIds, $recruiterIds) {

            $manageable = Enlistments::query()
                ->with('corporation.alliance')
                ->when(! $isSuperuser, fn (Builder $query) => $query->whereIn('corporation_id', $manageableIds))
                ->get()
                ->map(fn (Enlistments $enlistment) => $enlistment->setAttribute('can_manage', true));

            $recruitable = Enlistments::query()
                ->with('corporation.alliance')
                ->when(! $isSuperuser, fn (Builder $query) => $query->whereIn('corporation_id', $recruiterIds))
                ->whereNotIn('corporation_id', $manageableIds)
                ->get()
                ->map(fn (Enlistments $enlistment) => $enlistment->setAttribute('can_manage', false));

            return $manageable->concat($recruitable);
        });
    }
}
