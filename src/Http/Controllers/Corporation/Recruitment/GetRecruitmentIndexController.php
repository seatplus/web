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
use Inertia\Inertia;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;
use Seatplus\Eveapi\Models\Recruitment\Enlistments;
use Seatplus\Web\Http\Controllers\Controller;

class GetRecruitmentIndexController extends Controller
{
    public const MANAGEPERMISSION = 'can open or close corporations for recruitment';
    public const RECRUITERPERMISSION = 'can accept or deny applications';

    public function __invoke()
    {
        $can_manage_recruitment = auth()->user()->can(self::MANAGEPERMISSION);

        return Inertia::render('Corporation/Recruitment/RecruitmentIndex', [
            'canManageRecruitment' => $can_manage_recruitment,
            'enlistments' => $this->getEnlistments(),
            'activeSidebarElement' => 'corporation.recruitment',
        ]);
    }

    private function getEnlistments()
    {

        $manageable_enlistments = Enlistments::query()
            ->with('corporation.alliance')
            ->whereHas('corporation', function (Builder $query) {
                $query->whereAffiliatedCorporation(new AffiliationsDto(
                    permissions: [self::MANAGEPERMISSION],
                    user: auth()->user(),
                    corporation_roles: ['Director']
                ));
            })
            ->select(['*', DB::raw("'true' as can_manage")]);

        $recruitable_enlistments = Enlistments::query()
            ->with('corporation.alliance')
            ->whereNotIn('corporation_id', fn ($query) => $query->select('corporation_id')->from($manageable_enlistments))
            ->whereHas('corporation', function (Builder $query) {
                $query->whereAffiliatedCorporation(new AffiliationsDto(
                    permissions: [self::RECRUITERPERMISSION],
                    user: auth()->user(),
                    corporation_roles: ['Director']
                ));
            })
            ->select(['*', DB::raw("'false' as can_manage")]);

        return $manageable_enlistments->union($recruitable_enlistments)->get();
    }
}
