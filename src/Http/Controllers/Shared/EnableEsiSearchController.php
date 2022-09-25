<?php

namespace Seatplus\Web\Http\Controllers\Shared;

use Seatplus\Auth\Models\User;
use Seatplus\Web\Services\SearchService;

class EnableEsiSearchController
{
    public function __invoke()
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

        $user = User::query()
            ->with('characters.corporation')
            ->find(auth()->user()->getAuthIdentifier());

        return inertia('EnableEsiSearch', [
            'characters' => $user->characters->map(fn ($character) => [
                'character_id' => $character->character_id,
                'name' => $character->name,
                'corporation' => $character->corporation->name ?? 'Unknown Corporation',
                'upgrade_url' => route('auth.eve.step_up', [
                    'character_id' => $character->character_id,
                    'add_scopes' => 'esi-search.search_structures.v1',
                ]),
            ]),
        ]);
    }
}
