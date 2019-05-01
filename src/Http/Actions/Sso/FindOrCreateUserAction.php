<?php


namespace Seatplus\Web\Http\Actions\Sso;


use Laravel\Socialite\Two\User as EveUser;
use Seatplus\Web\Models\CharacterUser;
use Seatplus\Web\Models\User;

class FindOrCreateUserAction
{
    public function execute(EveUser $eve_user): User
    {

        $character_user = CharacterUser::where('character_id', $eve_user->character_id)->first();

        // If user is known and character_owner_hash didn't change return the user
        if (! empty($character_user) && $character_user->character_owner_hash === $eve_user->character_owner_hash)
            return User::find($character_user->user_id);

        /*
         * If user is known and character_owner_hash changed it means that the
         * character might have been transferred. We create a new user and
         * return the new user
         */
        if (! empty($character_user) && $character_user->character_owner_hash !== $eve_user->character_owner_hash)
            CharacterUser::where('character_id', $eve_user->character_id)->first()->delete();

        $user = User::forceCreate([  // Only because I don't want to set id as fillable
            'id'                   => $eve_user->character_id,
            'name'                 => $eve_user->name,
            'active'               => true,
            'character_owner_hash' => $eve_user->character_owner_hash,
        ]);

        $this->createCharacterUserEntry($user->id, $eve_user);

        return $user;

    }

    private function createCharacterUserEntry(int $user_id, EveUser $eve_user)
    {

        CharacterUser::create([
            'user_id'              => $user_id,
            'character_id'         => $eve_user->character_id,
            'character_owner_hash' => $eve_user->character_owner_hash,
        ]);
    }

}