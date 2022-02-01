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

namespace Seatplus\Web\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\BuildCharacterScopesArray;
use Seatplus\Auth\Services\BuildUserLevelRequiredScopes;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

class ApplicationRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $is_user = $this->applicationable_type === User::class;

        return [
            'application_id' => $this->id,
            'is_user' => $is_user,
            $this->mergeWhen($is_user, ['user' => $this->applicationable]),
            'main_character' => $is_user ? $this->applicationable->main_character : CharacterUser::query()->with('user.main_character')->firstWhere('character_id', $this->applicationable->character_id)->user->main_character,
            'characters' => $this->getCharacters(),
            'decision_count' => $this->decision_count,
        ];
    }

    private function buildCharacterArray(CharacterInfo $character): array
    {
        $user = ! $this->applicationable instanceof User
            ? null
            : $this->applicationable->loadMissing(
                'characters.alliance.ssoScopes',
                'characters.corporation.ssoScopes',
                'characters.alliance.ssoScopes',
                'characters.application.corporation.ssoScopes',
                'characters.application.corporation.alliance.ssoScopes',
                'characters.refresh_token',
                'application.corporation.ssoScopes',
                'application.corporation.alliance.ssoScopes'
            );

        // Get user level required scopes
        $user_scopes = $user ? BuildUserLevelRequiredScopes::get($user) : [];

        $character_scopes_array = BuildCharacterScopesArray::make()
            ->setCharacter($character)
            ->setUserScopes($user_scopes)
            ->get();

        return array_merge($character_scopes_array, $character->toArray());
    }

    private function getCharacters(): array
    {
        if ($this->applicationable instanceof User) {
            return $this->applicationable
                ->characters
                ->map(fn ($character) => $this->buildCharacterArray($character))
                ->toArray();
        }

        if ($this->applicationable instanceof CharacterInfo) {
            return [$this->buildCharacterArray($this->applicationable)];
        }

        return [];
    }
}
