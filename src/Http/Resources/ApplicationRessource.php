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

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Services\Recruitment\GetApplicationCharacterScopesService;

/**
 * @mixin Application
 */
class ApplicationRessource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $is_user = $this->applicationable_type === User::class;

        return [
            'application_id' => $this->id,
            'is_user' => $is_user,
            $this->mergeWhen($is_user, ['user' => $this->applicationable]),
            'mainCharacter' => $is_user ? data_get($this->applicationable, 'mainCharacter') : $this->getMainCharacterFromCharacterId(data_get($this->applicationable, 'character_id')),
            'characters' => $this->getCharacters(),
            'decision_count' => $this->decision_count,
        ];
    }

    private function getMainCharacterFromCharacterId(mixed $character_id): ?CharacterInfo
    {
        /** @var CharacterUser|null $character_user */
        $character_user = CharacterUser::query()
            ->with('user.mainCharacter')
            ->firstWhere('character_id', $character_id);

        /** @var User|null $user */
        $user = $character_user?->user;

        /** @var CharacterInfo|null $mainCharacter */
        $mainCharacter = $user?->mainCharacter;

        return $mainCharacter;
    }

    private function buildCharacterArray(CharacterInfo $character): array
    {
        $withApplicationScopes = $this->applicationable instanceof User;

        $scopeData = (new GetApplicationCharacterScopesService($withApplicationScopes))->get($character);

        return array_merge($scopeData, $character->toArray());
    }

    private function getCharacters(): array
    {
        if ($this->applicationable instanceof User) {
            return $this->applicationable
                ->characters
                ->map(fn (CharacterInfo $character) => $this->buildCharacterArray($character))
                ->toArray();
        }

        if ($this->applicationable instanceof CharacterInfo) {
            return [$this->buildCharacterArray($this->applicationable)];
        }

        return [];
    }
}
