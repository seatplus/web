<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

/**
 * Minimal, camelCase user shape for the add-member / add-moderator picker: the user id, their main
 * character (portrait + name), and every character's name (so a result found via an alt shows who
 * it is). Deliberately lighter than UserRessource — no scopes/corp/alliance — so a search doesn't
 * over-fetch (and doesn't touch relations that would need extra eager loading under strict mode).
 *
 * Expects `mainCharacter` and `characters` eager-loaded.
 *
 * @mixin User
 */
class UserSearchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mainCharacter' => $this->mainCharacter
                ? [
                    'character_id' => $this->mainCharacter->character_id,
                    'name' => $this->mainCharacter->name,
                ]
                : null,
            'characters' => $this->characters
                ->map(fn (CharacterInfo $character): array => [
                    'character_id' => $character->character_id,
                    'name' => $character->name,
                ])
                ->values(),
        ];
    }
}
