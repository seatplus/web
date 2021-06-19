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

namespace Seatplus\Web\Http\Controllers\Configuration;

use Inertia\Inertia;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Controllers\Controller;
use Seatplus\Web\Http\Resources\UserRessource;
use Seatplus\Web\Services\ImpersonateService;

class SeatPlusController extends Controller
{
    public function navigation()
    {
        $navigation_tabs = config('web.settings');

        return collect($navigation_tabs)->toJson();
    }

    public function settings()
    {
        $validatedData = request()->validate([
            'search_param' => 'string',
        ]);

        $query = User::with('characters', 'characters.alliance', 'characters.corporation', 'main_character.corporation');

        if (request()->has('search_param')) {
            $query = $query->search($validatedData['search_param']);
        }

        $users = UserRessource::collection(
            $query->paginate()
        );

        return Inertia::render('Configuration/UserList', [
            'users' => $users,
            'activeSidebarElement' => 'server.settings',
        ]);
    }

    public function impersonate($user_id)
    {
        $impersonated_user = User::find($user_id);

        (new ImpersonateService)->impersonateUser($impersonated_user);

        return redirect()->route('home')->with('success', 'Impersonating ' . $impersonated_user->main_character->name);
    }
}
