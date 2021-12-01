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

namespace Seatplus\Web\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Models\Recruitment\Enlistment;

class HomeController extends Controller
{
    public function home()
    {
        return Inertia::render('Dashboard/Index', [
            'characters' => CharacterInfo::with('corporation', 'alliance', 'application', 'balance', 'batch_update')
                ->whereIn('character_id', auth()->user()->characters->pluck('character_id')->toArray())
                ->get(),
        ]);
    }

    public function getEnlistments()
    {
        return Enlistment::with('corporation', 'corporation.alliance')->paginate();
    }

    public function getOwnApplications(int $corporation_id)
    {
        return Application::whereHasMorph(
            'applicationable',
            [User::class, CharacterInfo::class],
            function (Builder $query, $type) {
                match ($type) {
                    User::class => $query->where('id', auth()->user()->getAuthIdentifier()),
                    CharacterInfo::class => $query->whereIn('character_id', auth()->user()->characters()->pluck('character_infos.character_id')),
                };

                $query->whereStatus('open');
            }
        )->whereCorporationId($corporation_id)
            ->paginate();
    }
}
