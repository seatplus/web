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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Resources\UserRessource;
use Seatplus\Web\Services\Sidebar\SidebarEntries;

class HandleInertiaRequests extends Middleware
{
    // Set root template via method
    public function rootView(Request $request)
    {
        return 'web::app';
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'flash' => function () {
                return [
                    'success' => session()->pull('success'),
                    'info'    => session()->pull('info'),
                    'warning' => session()->pull('warning'),
                    'error'   => session()->pull('error'),
                ];
            },
            'sidebar' => function () {
                return auth()->guest() ? [] : (new SidebarEntries())->filter();
            },
            'user' => function () {
                return auth()->guest() ? '' : UserRessource::make(
                    User::with('main_character', 'characters', 'characters.refresh_token')
                        ->where('id', auth()->user()->getAuthIdentifier())
                        ->first()
                );
            },
            'translation' => function () {
                return [
                    'success' => trans('web::notifications.success'),
                    'info'    => trans('web::notifications.info'),
                    'warning' => trans('web::notifications.warning'),
                    'error'   => trans('web::notifications.error'),
                ];
            },
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
            'images' => function () {
                return [
                    'logo' => asset('img/seat_plus.svg'),
                ];
            },
        ]);
    }
}
