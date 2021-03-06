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

use Inertia\Inertia;
use Seatplus\Eveapi\Models\Recruitment\Enlistments;
use Seatplus\Web\Http\Controllers\Controller;

class GetRecruitmentIndexController extends Controller
{
    const MANAGEPERMISSION = 'can open or close corporations for recruitment';
    const RECRUITERPERMISSION = 'can accept or deny applications';

    public function __invoke()
    {
        $can_manage_recruitment = auth()->user()->can(self::MANAGEPERMISSION);

        return Inertia::render('Corporation/Recruitment/RecruitmentIndex', [
            'can_manage_recruitment' => $can_manage_recruitment,
            'corporations' => $this->getCorporations(),
            'activeSidebarElement' => 'corporation.recruitment',
        ]);
    }

    private function getCorporations()
    {
        $manageable_ids = getAffiliatedIdsByPermission(self::MANAGEPERMISSION);
        $recruitable_ids = getAffiliatedIdsByPermission(self::RECRUITERPERMISSION);

        return Enlistments::whereIn('corporation_id', [...$manageable_ids, ...$recruitable_ids])
            ->with('corporation.alliance')
            ->get()
            ->map(function ($enlistment) use ($manageable_ids, $recruitable_ids) {
                $corporation = $enlistment->corporation;
                $corporation->can_manage = in_array($enlistment->corporation_id, $manageable_ids);
                $corporation->can_recruit = in_array($enlistment->corporation_id, $recruitable_ids);

                return $corporation;
            });
    }
}
