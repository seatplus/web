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

namespace Seatplus\Web\Services;

use Illuminate\Support\Facades\Cache;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Containers\EsiRequestContainer;
use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Eveapi\Services\Facade\RetrieveEsiData;

class SearchService
{
    public function execute(RefreshToken $token, array $categories, string $term)
    {
        $container = new EsiRequestContainer(
            method: 'get',
            version: 'v3',
            endpoint: '/characters/{character_id}/search/',
            refresh_token: $token,
            path_values: [
                'character_id' => $token->character_id,
            ],
            query_parameters: [
                'categories' => implode(',', $categories),
                'search' => $term,
            ]
        );

        return RetrieveEsiData::execute($container);
    }

    public static function getTokenFromCurrentUser(): ?RefreshToken
    {
        $user_id = auth()->user()->getAuthIdentifier();

        return Cache::remember("esi-search:$user_id", now()->addHour()->diffInSeconds(), function () {
            $user = User::query()
                ->with('characters.refresh_token')
                ->find(auth()->user()->getAuthIdentifier());

            $tokens = $user->characters->map(fn ($character) => $character->refresh_token)->filter();

            return $tokens->firstWhere(fn ($token) => in_array('esi-search.search_structures.v1', $token->scopes));
        });
    }
}
