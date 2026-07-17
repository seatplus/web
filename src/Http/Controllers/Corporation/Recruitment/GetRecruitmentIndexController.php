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
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Recruitment\Enlistments;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\CorporationInfoRessource;

class GetRecruitmentIndexController extends Controller
{
    final public const string MANAGEPERMISSION = 'can open or close corporations for recruitment';

    final public const string RECRUITERPERMISSION = 'can accept or deny applications';

    public function __invoke(): Response
    {
        $user = auth()->user();
        $isSuperuser = $user->can('superuser');
        $canManageRecruitment = $user->can(self::MANAGEPERMISSION);

        $manageableIds = $this->getAffiliatedIds->get(
            permissions: [self::MANAGEPERMISSION],
            corporationRoles: ['Director'],
            user: $user,
        );

        return Inertia::render('Corporation/Recruitment/RecruitmentIndex', [
            'canManageRecruitment' => $canManageRecruitment,
            'enlistments' => $this->getEnlistments($isSuperuser, $manageableIds),
            // Corporations the manager may open for recruitment (i.e. affiliated + not yet
            // enlisted) as a native Inertia infinite-scroll prop, replacing the old
            // axios/Ziggy useInfinityScrolling loader inside CorporationList.vue.
            'corporations' => Inertia::scroll(
                fn (): LengthAwarePaginator => $this->getEnlistableCorporations($isSuperuser, $manageableIds),
            ),
            'activeSidebarElement' => 'corporation.recruitment',
        ]);
    }

    private function getEnlistments(bool $isSuperuser, array $manageableIds): Collection
    {

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

    private function getEnlistableCorporations(bool $isSuperuser, array $manageableIds): LengthAwarePaginator
    {
        return CorporationInfo::query()
            ->select('corporation_infos.*')
            ->when(! $isSuperuser, fn (Builder $query) => $query->whereIn('corporation_id', $manageableIds))
            ->whereNotIn('corporation_id', Enlistments::query()->select('corporation_id'))
            ->with('alliance')
            ->orderBy('name')
            ->paginate(pageName: 'corporations')
            ->through(fn (CorporationInfo $corporation): array => (new CorporationInfoRessource($corporation))->resolve());
    }
}
