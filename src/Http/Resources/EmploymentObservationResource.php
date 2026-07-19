<?php

namespace Seatplus\Web\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Enums\EmploymentStatus;
use Seatplus\Web\Services\Recruitment\GetApplicationCharacterScopesService;

/**
 * @mixin User
 */
class EmploymentObservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $lastLogon = $this->getAttribute('observation_last_logon');
        $lastLogonByCharacter = $lastLogon instanceof Collection ? $lastLogon : collect();

        $status = $this->getAttribute('observation_status');

        $characters = $this->characters->map(fn (CharacterInfo $character) => [
            'character_id' => $character->character_id,
            'name' => $character->name,
            'missing_scopes' => array_values($this->missingScopes($character)),
            'last_logon' => $lastLogonByCharacter->get($character->character_id),
        ]);

        $corporationId = $this->getAttribute('observation_corporation_id');

        return [
            'id' => $this->id,
            'main_character' => $this->mainCharacter,
            'characters' => $characters->all(),
            'count_missing' => $characters->filter(fn (array $character) => data_get($character, 'missing_scopes'))->count(),
            'count_complete' => $characters->reject(fn (array $character) => data_get($character, 'missing_scopes'))->count(),
            'count_total' => $characters->count(),
            'employment_status' => $status instanceof EmploymentStatus ? $status->value : null,
            'inspect_url' => route('employment.observe.member', [$corporationId, $this->getKey()]),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function missingScopes(CharacterInfo $character): array
    {
        $result = (new GetApplicationCharacterScopesService)->get($character);

        return data_get($result, 'missing_scopes', []);
    }
}
