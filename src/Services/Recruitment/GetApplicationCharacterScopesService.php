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
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE OF ARISING OUT
 * OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Seatplus\Web\Services\Recruitment;

use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\SsoScopes\GlobalSsoScopesService;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\SsoScopes;

class GetApplicationCharacterScopesService
{
    public function __construct(
        private readonly bool $withApplicationScopes = true,
        private ?GlobalSsoScopesService $globalSsoScopesService = null
    ) {
        $this->globalSsoScopesService = $this->globalSsoScopesService ?? new GlobalSsoScopesService;
    }

    /**
     * @return array{required_scopes: array<int,string>, missing_scopes: array<int,string>}
     */
    public function get(CharacterInfo $character): array
    {
        $character = $character->loadMissing([
            'corporation.ssoScopes',
            'alliance.ssoScopes',
            'application.corporation.ssoScopes',
            'application.corporation.alliance.ssoScopes',
            'refresh_token',
        ]);

        $user = User::whereHas('characters', fn (mixed $q) => $q->where('character_infos.character_id', $character->character_id))
            ->with([
                'characters.corporation',
                'characters.alliance',
                'application.corporation.ssoScopes',
                'application.corporation.alliance.ssoScopes',
            ])
            ->first();

        $required_scopes = array_unique(array_merge(
            $this->getGlobalScopes(),
            $this->getCorporationScopes($character),
            $this->getAllianceScopes($character),
            $this->getUserScopes($user),
            $this->getCharacterApplicationScopes($character),
            $this->getUserApplicationScopes($user),
        ));

        $token_scopes = $character->refresh_token->scopes ?? [];
        $missing_scopes = array_values(array_diff($required_scopes, $token_scopes));

        return [
            'required_scopes' => array_values($required_scopes),
            'missing_scopes' => $missing_scopes,
        ];
    }

    private function getGlobalScopes(): array
    {
        return collect($this->globalSsoScopesService->get())->flatten()->unique()->all();
    }

    private function getCorporationScopes(CharacterInfo $character): array
    {
        return $character->corporation->ssoScopes->selected_scopes ?? [];
    }

    private function getAllianceScopes(CharacterInfo $character): array
    {
        return $character->alliance->ssoScopes->selected_scopes ?? [];
    }

    private function getUserScopes(?User $user): array
    {
        if (! $user) {
            return [];
        }

        $corporation_ids = $user->characters->pluck('corporation.corporation_id')->unique()->all();
        $alliance_ids = $user->characters->pluck('alliance.alliance_id')->filter()->unique()->all();

        return SsoScopes::query()
            ->whereIn('morphable_id', [...$corporation_ids, ...$alliance_ids])
            ->where('type', 'user')
            ->pluck('selected_scopes')
            ->flatten()
            ->unique()
            ->all();
    }

    private function getCharacterApplicationScopes(CharacterInfo $character): array
    {
        if (! $this->withApplicationScopes) {
            return [];
        }

        return array_merge(
            $character->application->corporation->ssoScopes->selected_scopes ?? [],
            $character->application->corporation->alliance->ssoScopes->selected_scopes ?? []
        );
    }

    private function getUserApplicationScopes(?User $user): array
    {
        if (! $this->withApplicationScopes || ! $user) {
            return [];
        }

        return array_merge(
            $user->application->corporation->ssoScopes->selected_scopes ?? [],
            $user->application->corporation->alliance->ssoScopes->selected_scopes ?? []
        );
    }
}
