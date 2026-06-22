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
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Resources\UserRessource;
use Seatplus\Web\Services\Sidebar\SidebarEntries;

class HandleInertiaRequests extends Middleware
{
    public function rootView(Request $request): string
    {
        return 'web::app';
    }

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            // Resolved first (and lazily, when auth/session are safely available) so the
            // app locale is set before the trans() closures below and the @translations
            // blade directive in the Inertia root view render.
            'locale' => fn () => $this->resolveLocale($request),
            'locales' => fn () => config('web.locales', []),
            'activeSidebarElement' => $request->route()?->getName(),
            'flash' => fn () => [
                'success' => session()->pull('success'),
                'info' => session()->pull('info'),
                'warning' => session()->pull('warning'),
                'error' => session()->pull('error'),
            ],
            'sidebar' => fn () => auth()->guest() ? [] : (new SidebarEntries)->getFilteredEntries(),
            'user' => fn () => auth()->guest() ? '' : UserRessource::make(
                User::with([
                    'mainCharacter',
                    'characters' => [
                        'corporation',
                        'refreshToken',
                        'alliance',
                        'characterAffiliation',
                    ],
                ])
                    ->where('id', auth()->user()->getAuthIdentifier())
                    ->first()
            ),
            'translation' => fn () => [
                'success' => trans('web::notifications.success'),
                'info' => trans('web::notifications.info'),
                'warning' => trans('web::notifications.warning'),
                'error' => trans('web::notifications.error'),
            ],
            'errors' => fn () => Session::get('errors')
                ? Session::get('errors')->getBag('default')->getMessages()
                : (object) [],
            'images' => fn () => [
                'logo' => asset(config('web.images.logo')),
                'icon' => asset(config('web.images.icon')),
            ],
        ]);
    }

    /**
     * Resolve the request locale, set it on the application, and return it for the prop.
     *
     * Resolution order (first hit wins): authenticated user preference → session pick
     * (guests) → browser Accept-Language → global setting → app default. Only a locale in
     * the supported registry is honoured; anything else falls back to the app default.
     */
    private function resolveLocale(Request $request): string
    {
        $supported = array_keys(config('web.locales', []));
        $fallback = (string) config('app.locale');

        $candidate = $this->userLocale()
            ?? session('locale')
            ?? $this->browserLocale($request, $supported)
            ?? setting('language')
            ?? $fallback;

        $locale = in_array($candidate, $supported, true) ? (string) $candidate : $fallback;

        App::setLocale($locale);

        return $locale;
    }

    /**
     * The authenticated user's stored locale, or null. Read only when the column is loaded,
     * to stay compatible with Model::shouldBeStrict() before the auth package ships it.
     */
    private function userLocale(): ?string
    {
        if (! auth()->check() || ! array_key_exists('locale', auth()->user()->getAttributes())) {
            return null;
        }

        $locale = auth()->user()->getAttribute('locale');

        return is_string($locale) ? $locale : null;
    }

    /**
     * The first browser-preferred (Accept-Language) locale that we support, or null.
     *
     * @param  list<string>  $supported
     */
    private function browserLocale(Request $request, array $supported): ?string
    {
        foreach ($request->getLanguages() as $language) {
            $code = strtolower(substr((string) $language, 0, 2));

            if (in_array($code, $supported, true)) {
                return $code;
            }
        }

        return null;
    }
}
