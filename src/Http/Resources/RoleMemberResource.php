<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Seatplus\Auth\Models\AccessControl\RoleMembership;

/**
 * One User membership row of a role — a member, applicant, or moderator — reduced to what the
 * manage-members UI needs: the owning user id (for approve/deny/kick/moderator actions, which are
 * keyed by user_id), their main character for display, and the status / can_moderate flags.
 *
 * Expects the membership's `entity` (User) eager-loaded with its `mainCharacter`.
 *
 * @mixin RoleMembership
 */
class RoleMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $mainCharacterId = data_get($this->entity, 'mainCharacter.character_id');
        $mainCharacterName = data_get($this->entity, 'mainCharacter.name');

        return [
            'user_id' => (int) $this->entity_id,
            'status' => $this->status,
            'can_moderate' => (bool) $this->can_moderate,
            'character' => [
                'character_id' => is_int($mainCharacterId) ? $mainCharacterId : (int) $mainCharacterId,
                'name' => is_string($mainCharacterName) ? $mainCharacterName : null,
            ],
        ];
    }
}
