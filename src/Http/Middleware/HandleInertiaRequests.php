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
use Inertia\Inertia;
use Inertia\Middleware;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Actions\Shared\GetAffiliatedEntitiesAction;
use Seatplus\Web\Http\Resources\UserRessource;
use Seatplus\Web\Services\Sidebar\SidebarEntries;
use Seatplus\Web\Support\Locales;
use Seatplus\Web\Support\Translations;

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
            // Locale is resolved + set by the SetLocale middleware before controllers/props run.
            'locale' => fn () => app()->getLocale(),
            'locales' => fn () => Locales::available(),
            // Shared chrome translations (baseline) — the notification labels the persistent
            // layout (Toast.vue) needs on every page. Page-specific groups arrive as a
            // `pageTranslations` prop and are merged client-side.
            'translations' => fn () => Translations::gather(['web::notifications']),
            'activeSidebarElement' => $request->route()?->getName(),
            // Resolved server-side so the sidebar's user-settings link needs no Ziggy route().
            'userSettingsUri' => fn () => route('user.settings'),
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
            'errors' => fn () => Session::get('errors')
                ? Session::get('errors')->getBag('default')->getMessages()
                : (object) [],
            'images' => fn () => [
                'logo' => asset(config('web.images.logo')),
                'icon' => asset(config('web.images.icon')),
            ],
            // Server-settings navigation tabs. Static config (name/logo/route per tab), so it is
            // shared eagerly as page chrome rather than fetched post-mount by Settings.vue — which
            // removes an axios + Ziggy round-trip and the flash of empty tabs.
            // Each entry's target URL is resolved server-side so the settings tabs need no Ziggy route().
            'settingsNavigation' => fn () => collect(config('web.settings'))
                ->map(fn (array $entry) => [...$entry, 'url' => route($entry['route'])])
                ->all(),
            // The entity-picker slide-over's affiliated character/corporation list. Declared
            // `optional` so it is NOT resolved on ordinary page loads (no DB query, no cost) —
            // only when the picker's <WhenVisible data="affiliatedEntities"> scrolls into view and
            // issues a partial reload for exactly this prop. The picker context (`type`,
            // `permission`, `corporationRoles`, `search`) rides along as request data on that reload
            // so the same shared prop can serve every character/corporation page without any
            // per-controller wiring. Delegates to the same action the standalone
            // GetAffiliated{Characters,Corporations} endpoints use, so the query lives in one place.
            'affiliatedEntities' => Inertia::optional(fn () => $this->resolveAffiliatedEntities($request)),
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function resolveAffiliatedEntities(Request $request): array
    {
        if (auth()->guest()) {
            return [];
        }

        $permission = $request->input('permission');

        if (blank($permission)) {
            return [];
        }

        // `search_aff` (not `search`) so the picker's query never collides with a page's own
        // `search` filter (e.g. the assets page) when this prop reloads via <WhenVisible>.
        $search = $request->input('search_aff') ?: null;
        $action = app(GetAffiliatedEntitiesAction::class);

        if ($request->input('type') === 'corporation') {
            $corporationRoles = collect(explode(',', (string) $request->input('corporationRoles', '')))
                ->map(fn (string $role): string => trim($role))
                ->filter()
                ->values()
                ->all();

            return $action->corporations($permission, $corporationRoles, $search)->resolve();
        }

        return $action->characters($permission, $search)->resolve();
    }
}
