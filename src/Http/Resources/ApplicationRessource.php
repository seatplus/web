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
        return [
            'is_user' => $this->applicationable_type === User::class,
            $this->mergeWhen($this->applicationable_type === User::class, [
                'user' => $this->applicationable,
            ]),
            'main_character' => $this->applicationable->main_character ?? null,
            'characters' => $this->applicationable instanceof User ? $this->applicationable->characters->map(fn ($character) => $this->buildCharacterArray($character)) : [],
            'character' => $this->applicationable instanceof CharacterInfo ? $this->buildCharacterArray($this->applicationable) : [],
        ];
    }

    private function getRequiredScopes(CharacterInfo $character): array
    {
        // Add global required scopes
        $global_scope = setting('global_sso_scopes');

        return collect([
            'corporation_scopes'             => $character->corporation->ssoScopes->selected_scopes ?? [],
            'alliance_scopes'                => $character->alliance->ssoScopes->selected_scopes ?? [],
            'character_application_corporation_scopes' => $character->application->corporation->ssoScopes->selected_scopes ?? [],
            'character_application_alliance_scopes'    => $character->application->corporation->alliance->ssoScopes->selected_scopes ?? [],
            'global_scopes'                  => json_decode($this->user->global_scope) ?? [],
            'user_application_corporation_scopes' => $this->applicationable->application->corporation->ssoScopes->selected_scopes ?? [],
            'user_application_alliance_scopes'    => $this->applicationable->application->corporation->alliance->ssoScopes->selected_scopes ?? [],
        ])->flatten(1)
            ->filter()
            ->unique()
            ->flatten(1)
            ->toArray();
    }

    private function buildCharacterArray(CharacterInfo $character)
    {
        $user = ! $this->applicationable instanceof User
            ? null
            : $this->applicationable->loadMissing('characters.alliance.ssoScopes',
                'characters.corporation.ssoScopes',
                'characters.alliance.ssoScopes',
                'characters.application.corporation.ssoScopes',
                'characters.application.corporation.alliance.ssoScopes',
                'characters.refresh_token',
                'application.corporation.ssoScopes',
                'application.corporation.alliance.ssoScopes');

        // Get user level required scopes
        $user_scopes = $user ? BuildUserLevelRequiredScopes::get($user) : [];

        return BuildCharacterScopesArray::make()
            ->setCharacter($character)
            ->setUserScopes($user_scopes)
            ->get();
    }
}
