<?php


namespace Seatplus\Web\Tests;


use Seatplus\Eveapi\EveapiServiceProvider;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Http\Actions\Sso\FindOrCreateUserAction;
use Seatplus\Web\Models\CharacterUser;
use Seatplus\Web\Models\User;
use Laravel\Socialite\Contracts\Factory as Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

class FindOrCreateUserActionTest extends TestCase
{
    /*User {#236 ▼
        +token: "maPOZCRtQCx8r6AWjzBMzwwmu-_E42IgqApzpUax8gYvVI0ucio9-pX99mEivX2fhct8ya4iS0ppgy_DJ0MXFA"
        +refreshToken: null
        +expiresIn: null
        +id: null
        +nickname: null
        +name: "Herpaderp Aldent"
        +email: null
        +avatar: "https://image.eveonline.com/Character/95725047_128.jpg"
        +user: array:8 [▼
    "CharacterID" => 95725047
    "CharacterName" => "Herpaderp Aldent"
    "ExpiresOn" => "2019-05-01T15:56:00"
    "Scopes" => "publicData"
    "TokenType" => "Character"
    "CharacterOwnerHash" => "TRVh/ElZN1oo+lsYJ5R+khBV+KE="
    "IntellectualProperty" => "EVE"
    "RefreshToken" => "XrlTt7w1DtQAZnQeYxPmTjmxYBwuO91ABsQCWSVEN7U"
  ]
  +"character_id": 95725047
        +"character_owner_hash": "TRVh/ElZN1oo+lsYJ5R+khBV+KE="
        +"scopes": "publicData"
        +"refresh_token": "XrlTt7w1DtQAZnQeYxPmTjmxYBwuO91ABsQCWSVEN7U"
        +"expires_on": Carbon @1556726160 {#245 ▶}
        }*/

    /** @test */
    public function createNewUser()
    {

        $socialiteUser = $this->createMock(SocialiteUser::class);
        $socialiteUser->character_id = 1337;
        $socialiteUser->name = 'SocialiteUserName';
        $socialiteUser->character_owner_hash = 'h453dV4lu3';

        $user = (new FindOrCreateUserAction)->execute($socialiteUser);

        $this->assertDatabaseHas('users', [
            'name' => $user->name,
        ]);

        $this->assertDatabaseHas('character_users', [
            'user_id' => $user->id,
            'character_id' => $user->id,
        ]);
    }

    /** @test */
    public function findExistingUserWithTwoCharacter()
    {

        // 1. create a user
        $test_user = factory(User::class)->create();

        // 2. add two characters
        factory(CharacterUser::class)->create([
            'user_id' => $test_user->id,
            'character_id' => $test_user->id
        ]);

        $second_character = factory(CharacterUser::class)->create([
            'user_id' => $test_user->id,
        ]);

        // 3. find user

        $socialiteUser = $this->createMock(SocialiteUser::class);
        $socialiteUser->character_id = $second_character->character_id;
        $socialiteUser->name = 'SocialiteUserName';
        $socialiteUser->character_owner_hash = $second_character->character_owner_hash;

        $user = (new FindOrCreateUserAction)->execute($socialiteUser);

        $this->assertDatabaseMissing('users', [
            'name' => $socialiteUser->name,
        ]);

        $this->assertDatabaseHas('character_users', [
            'user_id' => $test_user->id,
            'character_id' => $user->id,
        ]);
    }

    public function dealWithChangedOwnerHash()
    {

        // 1. create a user
        $test_user = factory(User::class)->create();

        // 2. create character_users entry
        factory(CharacterUser::class)->create([
            'user_id' => $test_user->id,
            'character_id' => $test_user->id,
            'character_owner_hash' => $test_user->character_owner_hash
        ]);

        // 3. find user

        $socialiteUser = $this->createMock(SocialiteUser::class);
        $socialiteUser->character_id = $test_user->id;
        $socialiteUser->name = $test_user->name;
        $socialiteUser->character_owner_hash = 'anotherHashValue';

        $user = (new FindOrCreateUserAction)->execute($socialiteUser);

        $this->assertDatabaseHas('users', [
            'id' => $socialiteUser->character_id,
        ]);

        $this->assertDatabaseHas('character_users', [
            'user_id' => $test_user->id,
            'character_id' => $user->id,
        ]);
    }

    public function dealWithTwoCharactersWithOneChangedOwnerHash()
    {

        // 1. create a user
        $test_user = factory(User::class)->create();

        // 2. create character_users entry for characters
        factory(CharacterUser::class)->create([
            'user_id' => $test_user->id,
            'character_id' => $test_user->id,
            'character_owner_hash' => $test_user->character_owner_hash
        ]);

        $secondary_user = factory(CharacterUser::class)->create([
            'user_id' => $test_user->id,
        ]);

        // 3. find user

        $socialiteUser = $this->createMock(SocialiteUser::class);
        $socialiteUser->character_id = $secondary_user->id;
        $socialiteUser->name = $secondary_user->name;
        $socialiteUser->character_owner_hash = 'anotherHashValue';

        $user = (new FindOrCreateUserAction)->execute($socialiteUser);

        // 4. assert that two users exist
        $this->assertDatabaseHas('users', [
            'id' => $socialiteUser->character_id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $secondary_user->character_id,
        ]);

        //5. assert that secondary character is not affiliated to first user

        $this->assertDatabaseMissing('character_users', [
            'user_id' => $test_user->id,
            'character_id' => $secondary_user->id,
        ]);

        $this->assertDatabasHas('character_users', [
            'user_id' => $test_user->id,
            'character_id' => $test_user->id,
        ]);

        $this->assertDatabasHas('character_users', [
            'user_id' => $secondary_user->id,
            'character_id' => $secondary_user->id,
        ]);
    }

}