<?php

namespace Seatplus\Web\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Seatplus\Auth\Services\BuildCharacterScopesArray;

class CorporationComplianceResource extends JsonResource
{
    public function toArray($request)
    {
        $characters = $this->characters->map(fn($character) => [
            'character_id' => $character->character_id,
            'name' => $character->name,
            'missing_scopes' => $this->getMissingScopes($character),
        ]);

        return [
            'main_character' => $this->main_character,
            'characters' => $characters,
            'count_missing' => collect($characters)->filter(fn($character) => data_get($character, 'missing_scope'))->count(),
            'count_complete' => collect($characters)->reject(fn($character) => data_get($character, 'missing_scope'))->count(),
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