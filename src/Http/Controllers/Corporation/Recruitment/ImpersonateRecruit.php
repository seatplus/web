<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
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

use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Applications;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Services\ImpersonateService;

class ImpersonateRecruit extends Controller
{
    const RECRUITERPERMISSION = 'can accept or deny applications';

    public function __invoke(User $user)
    {
        $affiliated_ids = getAffiliatedIdsByPermission(self::RECRUITERPERMISSION);

        $application = Applications::whereIn('corporation_id', $affiliated_ids)
            ->where('applicationable_type', User::class)
            ->with('applicationable')
            ->get()
            ->filter(fn ($application) => $application->id === $user->id)
            ->first();

        if (is_null($application)) {
            return redirect()->back()->with('error', 'You are not allowed to impersonate the user');
        }

        (new ImpersonateService)->impersonateUser($user, 'corporation.recruitment');

        return redirect()->route('home')->with('success', 'Impersonating ' . $user->main_character->name);
    }
}
