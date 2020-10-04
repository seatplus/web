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

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Jobs\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Applications;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\ApplicationRessource;

class GetApplicationsController extends Controller
{
    const RECRUITERPERMISSION = 'can accept or deny applications';

    public function __invoke(int $corporation_id)
    {
        abort_unless(auth()->user()->can(self::RECRUITERPERMISSION) && in_array($corporation_id, getAffiliatedIdsByPermission(self::RECRUITERPERMISSION)), 403, 'You have no rights accessing this data');

        $applications = Applications::where('corporation_id', $corporation_id)
            ->with([
                'applicationable' => fn (MorphTo $morph_to) => $morph_to->morphWith([
                    User::class => ['characters.refresh_token', 'main_character', 'characters.application.corporation.ssoScopes', 'characters.application.corporation.alliance.ssoScopes'],
                    CharacterInfo::class => ['refresh_token', 'application.corporation.ssoScopes', 'application.corporation.alliance.ssoScopes'],
                ]),
            ]);

        return ApplicationRessource::collection($applications->paginate());
    }
}
