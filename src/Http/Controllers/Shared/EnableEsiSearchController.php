<?php

namespace Seatplus\Web\Http\Controllers\Shared;

use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Services\SearchService;

class EnableEsiSearchController
{
    public function __invoke(): RedirectResponse|Response
    {
        // First check if user has already an esi search token
        if (SearchService::getTokenFromCurrentUser()) {
            // If so, redirect to intended url or home
            return redirect(session()->pull('from', '/'));
        }

        // If session has no from url, set it to previous url
        if (! session()->has('from')) {
            session()->put('from', url()->previous());
        }

        // The relative path to return to after the step-up. Passed through to StepUpController's
        // `redirect` param so CallbackController's intended() lands the user back on the page that
        // needed ESI search (e.g. /acl/create) instead of the dashboard.
        $return_to = $this->relativeReturnPath(session('from'));

        $user = User::query()
            ->with('characters.corporation')
            ->find(auth()->user()->getAuthIdentifier());

        return inertia('EnableEsiSearch', [
            'characters' => $user->characters->map(fn (CharacterInfo $character) => [
                'character_id' => $character->character_id,
                'name' => $character->name,
                'corporation' => $character->corporation->name ?? 'Unknown Corporation',
                'upgrade_url' => route('auth.eve.step_up', [
                    'character_id' => $character->character_id,
                    'add_scopes' => 'esi-search.search_structures.v1',
                    'redirect' => $return_to,
                ]),
            ]),
        ]);
    }

    /**
     * Reduce a possibly-absolute origin URL to a relative "path?query" — StepUpController only
     * accepts a `redirect` that starts with a single "/" (open-redirect guard), so an absolute
     * URL would be rejected and fall back to the dashboard.
     */
    private function relativeReturnPath(?string $url): string
    {
        if (! is_string($url) || $url === '') {
            return '/';
        }

        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $query = parse_url($url, PHP_URL_QUERY);

        return $query ? "{$path}?{$query}" : $path;
    }
}
