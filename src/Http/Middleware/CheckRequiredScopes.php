<?php


namespace Seatplus\Web\Http\Middleware;

use Illuminate\Support\Collection;
use Inertia\Inertia;
use Seatplus\Auth\Http\Middleware\CheckRequiredScopes as CheckRequiredScopesMiddleware;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

class CheckRequiredScopes extends CheckRequiredScopesMiddleware
{

    protected function redirectTo(Collection $missing_character_scopes)
    {

        $missing_character = $missing_character_scopes->map(function ($missing) {

            $missing = (object) $missing;

            return [
                'character_id' => $missing->character->character_id,
                'name' => $missing->character->name,
                'corporation' => CharacterInfo::find($missing->character->character_id)->corporation->name,
                'upgrade_url' => route('auth.eve', [
                    'character_id' => $missing->character->character_id,
                    'add_scopes' => implode(',', $missing->missing_scopes)
                    ])
            ];
        });

        return Inertia::render('Auth/MissingRequiredScopes', [
            'characters' => $missing_character
        ]);


        //TODO Build view.
    }
}
