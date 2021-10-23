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
use Seatplus\Auth\Services\BuildCharacterScopesArray;

class CorporationComplianceResource extends JsonResource
{
    public function toArray($request)
    {
        $characters = $this->characters->map(fn ($character) => [
            'character_id' => $character->character_id,
            'name' => $character->name,
            'missing_scopes' => $this->getMissingScopes($character),
        ]);

        return [
            'main_character' => $this->main_character,
            'characters' => $characters,
            'count_missing' => collect($characters)->filter(fn ($character) => data_get($character, 'missing_scope'))->count(),
            'count_complete' => collect($characters)->reject(fn ($character) => data_get($character, 'missing_scope'))->count(),
            'count_total' => collect($characters)->count(),
        ];
    }

    private function getMissingScopes($character): array
    {
        $character_scopes_array = BuildCharacterScopesArray::make()
            ->setCharacter($character)
            ->get();

        return data_get($character_scopes_array, 'missing_scopes', []);
    }
}
