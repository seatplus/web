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
                    'add_scopes' => implode(',', $missing->missing_scopes),
                ]),
            ];
        });

        return Inertia::render('Auth/MissingRequiredScopes', [
            'characters' => $missing_character,
        ]);

        //TODO Build view.
    }
}
