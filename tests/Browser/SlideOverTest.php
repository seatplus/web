<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\Permissions\Affiliation;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;

require_once __DIR__.'/helpers.php';

/*
 * SlideOver browser tests — the two slide-over surfaces that sit on the character/corporation
 * pages, both run against the real assembled core app:
 *
 *  1. The "Update" (dispatch) sidebar — lists the entities whose ESI data the user can refresh,
 *     split into owned ("Your characters"/"Your corporations", eager) and "Affiliated …" (lazy).
 *  2. The "Select character/corporation" entity picker — the axios/Ziggy-free rewrite that lists
 *     the entities the user may view. The affiliated list is NOT fetched up front: it rides on the
 *     lazily-resolved `affiliatedEntities` Inertia shared prop and is pulled in via <WhenVisible>
 *     only once the slide-over opens and the list scrolls into view. You tick a subset, and on close
 *     the picker navigates the page with `character_ids[]`/`corporation_ids[]` so the list filters.
 *
 * Both teleport their panel into <div id="destination"> (app.blade.php), outside the page's own
 * content, so #destination-scoped assertions (slideOverSees) tell the panel apart from the page's
 * own character/corporation cards. Provisioning helpers (actingAsCharacter / giveCorporationRole)
 * live in the synced browser Pest.php.
 */

uses(RefreshDatabase::class);

/*
 * --- "Update" (dispatch) sidebar -------------------------------------------------------------
 */

it('lists the user\'s own character in the update sidebar', function (string $device) {
    $character = actingAsCharacter();
    updateRefreshTokenWithScopes($character->refreshToken, config('eveapi.scopes.character.wallet'));

    $page = deviceVisit($device, '/character/wallets');
    $page->assertNoSmoke();

    $page->click('Update');
    $page->waitForText('Your characters');
    $page->assertScript(slideOverSees($character->name));

    snap($page, "dispatch-owned-character-{$device}");
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
    $page->assertScript(slideOverSees($character->name));
    $page->assertScript('! '.slideOverSees($affiliated_character->name));

    // Expanding the affiliated section lazily loads the non-owned character into the sidebar.
    $page->click('Affiliated characters');
    $page->assertScript(slideOverSees($affiliated_character->name));

    snap($page, "dispatch-affiliated-character-{$device}");
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
    $page->assertScript(slideOverSees($character->corporation->name));

    snap($page, "dispatch-owned-corporation-{$device}");
})->with(['desktop', 'iphone']);

/*
 * --- Entity picker ("Select character") — axios/Ziggy-free rewrite ---------------------------
 *
 * The assets page is used because its permission ('assets') maps to the CharacterInfo `assets()`
 * relation the controller filters on with `->has($permission)`, so a character with an asset is
 * listed. Both characters get an asset for that reason.
 */

it('picks a character in the entity slide-over and filters the page by the selection', function (string $device) {
    $owned = actingAsCharacter();
    Asset::factory()->create([
        'assetable_id' => $owned->character_id,
        'assetable_type' => CharacterInfo::class,
    ]);

    $user = User::whereMainCharacterId($owned->character_id)->sole();

    // A second character the acting user does NOT own, made viewable through a role carrying the
    // assets permission with an `allowed` affiliation to it — so GetAffiliatedIds resolves it and
    // it appears in the picker alongside the owned character.
    $affiliatedOwner = Event::fakeFor(fn () => User::factory()->create());
    $affiliated = $affiliatedOwner->characters->first();
    Asset::factory()->create([
        'assetable_id' => $affiliated->character_id,
        'assetable_type' => CharacterInfo::class,
    ]);

    $role = Role::create(['name' => 'Assets affiliation']);
    $role->givePermissionTo(Permission::findOrCreate(config('eveapi.permissions.'.Asset::class)));
    Affiliation::create([
        'role_id' => $role->id,
        'affiliatable_id' => $affiliated->character_id,
        'affiliatable_type' => CharacterInfo::class,
        'type' => 'allowed',
    ]);
    $user->assignRole($role);

    $page = deviceVisit($device, '/character/assets');
    $page->assertNoSmoke();

    // The affiliated list is lazy: before the picker opens, its slide-over (and the <WhenVisible>
    // inside it) is not mounted, so the `affiliatedEntities` shared prop has never been requested
    // and neither character is present in the teleported panel. #destination is empty here — the
    // pages render their own character cards, hence the #destination scope.
    $page->assertScript('! '.slideOverSees($owned->name));
    $page->assertScript('! '.slideOverSees($affiliated->name));

    // Open the picker: the SlideOver teleports into #destination, and <WhenVisible> fires a partial
    // reload for the `affiliatedEntities` prop once the list scrolls into view — loading both
    // characters on demand (WhenVisible fired) rather than eagerly on page load.
    $page->click('Select Character');
    $page->waitForText('Select character');
    $page->waitForText($owned->name);
    $page->assertScript(slideOverSees($owned->name));
    $page->assertScript(slideOverSees($affiliated->name));

    snap($page, "entity-picker-open-{$device}");

    // Tick the owned character, then close the panel — closing unmounts EntitySelection, whose
    // beforeUnmount navigates with the selection. Both actions scoped to #destination so the
    // page's own character cards can't be hit by accident.
    $page->script('(() => { const el = [...document.querySelectorAll("#destination li")].find((li) => li.innerText.includes('.json_encode($owned->name).')); if (el) { el.click(); } })()');
    $page->script('document.querySelector(\'#destination button[aria-label="Close panel"]\')?.click()');

    // The page reloads filtered to the picked character: its id is in the query string, the
    // non-selected one is not, and the "has selection" amber dot is now rendered.
    $page->assertScript('window.location.search.includes("character_ids")');
    $page->assertScript('window.location.search.includes('.json_encode((string) $owned->character_id).')');
    $page->assertScript('! window.location.search.includes('.json_encode((string) $affiliated->character_id).')');
    $page->assertScript('!! document.querySelector(".bg-amber-400")');

    snap($page, "entity-picker-selection-applied-{$device}");
})->with(['desktop', 'iphone']);
