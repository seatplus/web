<?php

namespace Seatplus\Web\Http\Actions\Recruitment;

use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

class DeleteCharacterApplicationAction
{
    private User $user;

    private int $character_id;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    public function execute(int $character_id): void
    {
        $this->character_id = $character_id;

        abort_unless($this->characterIdBelongsToUser(), 403, 'submitted character_id does not belong to user');

        $this->removeApplication();
    }

    private function characterIdBelongsToUser(): bool
    {
        return in_array($this->character_id, $this->getOwnedIds());
    }

    private function getOwnedIds(): array
    {
        return $this->user->characters->pluck('character_id')->toArray();
    }

    public function removeApplication(): void
    {
        CharacterInfo::find($this->character_id)->application()->delete();
    }
}
