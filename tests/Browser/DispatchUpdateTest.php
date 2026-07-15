<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Models\Permissions\Affiliation;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;

if (! function_exists('deviceVisit')) {
    /**
     * Visit $url on the given viewport ("desktop" or "iphone"). Browser tests run in the core app,
     * whose tests/Pest.php is not overlaid, so this helper is defined here (guarded) alongside the
     * suite's other function_exists helpers rather than in tests/Pest.php.
     */
    function deviceVisit(string $device, string $url, array $options = []): mixed
    {
        // iPhone: build a persistent page at the mobile viewport the same way visit() builds the
        // desktop one, so the page loads mobile from the start (no desktop-load-then-resize reflow,
        // no per-call re-navigation like ->on()->iPhone15()).
        if ($device === 'iphone') {
            return new PendingAwaitablePage(
                Playwright::defaultBrowserType(),
                Device::IPHONE_15,
                $url,
                $options,
            );
        }

        return visit($url, $options);
    }
}

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

// The Update SlideOver teleports into <div id="destination"> (app.blade.php), which is
// outside the page's own content. The wallet pages also render a card per character/
// corporation with the same name, so page-wide assertSee/assertDontSee can't tell the
// sidebar apart — scope assertions to #destination.
if (! function_exists('dispatchSidebarSees')) {
    function dispatchSidebarSees(string $text): string
    {
        return "(document.querySelector('#destination')?.innerText ?? '').includes(".json_encode($text).')';
    }
}

it('lists the user\'s own character in the update sidebar', function (string $device) {
    $character = actingAsCharacter();
    updateRefreshTokenWithScopes($character->refreshToken, config('eveapi.scopes.character.wallet'));

    $page = deviceVisit($device, '/character/wallets');
    $page->assertNoSmoke();

    $page->click('Update');
    $page->waitForText('Your characters');
    $page->assertScript(dispatchSidebarSees($character->name));

    $page->screenshot(true, "dispatch-owned-character-{$device}");
})->with(['desktop', 'iphone']);

it('reveals an affiliated (non-owned) character only when the affiliated section is expanded', function (string $device) {
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

    $page = deviceVisit($device, '/character/wallets');
    $page->assertNoSmoke();

    $page->click('Update');
    $page->waitForText('Your characters');

    // Owned section shows the user's own character; the affiliated one isn't in the
    // sidebar yet (it also renders as a page card, hence the #destination scope).
    $page->assertScript(dispatchSidebarSees($character->name));
    $page->assertScript('! '.dispatchSidebarSees($affiliated_character->name));

    // Expanding the affiliated section lazily loads the non-owned character into the sidebar.
    $page->click('Affiliated characters');
    $page->assertScript(dispatchSidebarSees($affiliated_character->name));

    $page->screenshot(true, "dispatch-affiliated-character-{$device}");
})->with(['desktop', 'iphone']);

it('lists the corporation in the update sidebar on a corporation-scoped page', function (string $device) {
    $character = actingAsCharacter();

    // Director grants access to the corporation wallet page; Accountant satisfies the
    // dispatch corp-wallet role filter (required_corporation_role = Accountant / Junior_Accountant).
    CharacterRole::updateOrCreate(
        ['character_id' => $character->character_id],
        ['roles' => ['Director', 'Accountant']],
    );
    updateRefreshTokenWithScopes($character->refreshToken, config('eveapi.scopes.corporation.wallet'));

    $page = deviceVisit($device, '/corporation/wallet');
    $page->assertNoSmoke();

    $page->click('Update');
    $page->waitForText('Your corporations');
    $page->assertScript(dispatchSidebarSees($character->corporation->name));

    $page->screenshot(true, "dispatch-owned-corporation-{$device}");
})->with(['desktop', 'iphone']);
