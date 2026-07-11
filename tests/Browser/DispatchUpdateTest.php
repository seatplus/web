<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\Permissions\Affiliation;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;

/*
 * "Update" (dispatch) sidebar browser tests — run against the real assembled core app.
 *
 * The Update button opens a SlideOver that lists the entities whose ESI data the user
 * can refresh, split into two sections: "Your characters"/"Your corporations" (owned,
 * loaded eagerly) and "Affiliated …" (loaded lazily when expanded). Each entry shows the
 * character/corporation name. getEntities filters RefreshTokens by the job's required
 * scope, so every entity here is given a scoped token. Provisioning helpers
 * (actingAsCharacter / giveCorporationRole / updateRefreshTokenWithScopes) live in the
 * synced browser Pest.php.
 */

uses(RefreshDatabase::class);

// updateRefreshTokenWithScopes lives in the web package's tests/Pest.php, which is NOT
// loaded in the core browser context (only actingAsCharacter/giveCorporationRole are).
// Define it here, guarded, so the synced browser test has it.
if (! function_exists('updateRefreshTokenWithScopes')) {
    function updateRefreshTokenWithScopes(RefreshToken $refreshToken, array $scopes): RefreshToken
    {
        $helper_token = RefreshToken::factory()->scopes($scopes)->make([
            'character_id' => $refreshToken->character_id,
        ]);

        $refreshToken->token = $helper_token->token;
        $refreshToken->save();

        return $refreshToken;
    }
}

it('lists the user\'s own character in the update sidebar', function () {
    $character = actingAsCharacter();
    updateRefreshTokenWithScopes($character->refreshToken, config('eveapi.scopes.character.wallet'));

    $page = visit('/character/wallets');
    $page->assertNoSmoke();

    $page->click('Update');
    $page->waitForText('Your characters');
    $page->assertSee($character->name);

    $page->screenshot(true, 'dispatch-owned-character');
});

it('reveals an affiliated (non-owned) character only when the affiliated section is expanded', function () {
    $character = actingAsCharacter();
    updateRefreshTokenWithScopes($character->refreshToken, config('eveapi.scopes.character.wallet'));
    $user = User::whereMainCharacterId($character->character_id)->sole();

    // A second character owned by someone else — the acting user does not own it.
    $affiliated_owner = Event::fakeFor(fn () => User::factory()->create());
    $affiliated_character = $affiliated_owner->characters->first();
    updateRefreshTokenWithScopes($affiliated_character->refreshToken, config('eveapi.scopes.character.wallet'));

    // Grant the acting user real access to it: a role carrying the wallet permission with
    // an `allowed` affiliation to that character. GetAffiliatedIds then resolves it as
    // affiliated (not owned).
    $role = Role::create(['name' => 'Wallet affiliation']);
    $role->givePermissionTo(Permission::findOrCreate(config('eveapi.permissions.'.WalletJournal::class)));
    Affiliation::create([
        'role_id' => $role->id,
        'affiliatable_id' => $affiliated_character->character_id,
        'affiliatable_type' => CharacterInfo::class,
        'type' => 'allowed',
    ]);
    $user->assignRole($role);

    $page = visit('/character/wallets');
    $page->assertNoSmoke();

    $page->click('Update');
    $page->waitForText('Your characters');

    // Owned section shows the user's own character; the affiliated one is not loaded yet.
    $page->assertSee($character->name);
    $page->assertDontSee($affiliated_character->name);

    // Expanding the affiliated section lazily loads the non-owned character.
    $page->click('Affiliated characters');
    $page->waitForText($affiliated_character->name);
    $page->assertSee($affiliated_character->name);

    $page->screenshot(true, 'dispatch-affiliated-character');
});

it('lists the corporation in the update sidebar on a corporation-scoped page', function () {
    $character = actingAsCharacter();

    // Director grants access to the corporation wallet page; Accountant satisfies the
    // dispatch corp-wallet role filter (required_corporation_role = Accountant / Junior_Accountant).
    CharacterRole::updateOrCreate(
        ['character_id' => $character->character_id],
        ['roles' => ['Director', 'Accountant']],
    );
    updateRefreshTokenWithScopes($character->refreshToken, config('eveapi.scopes.corporation.wallet'));

    $page = visit('/corporation/wallets');
    $page->assertNoSmoke();

    $page->click('Update');
    $page->waitForText('Your corporations');
    $page->assertSee($character->corporation->name);

    $page->screenshot(true, 'dispatch-owned-corporation');
});
