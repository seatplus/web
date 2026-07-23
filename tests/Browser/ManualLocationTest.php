<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Web\Models\ManualLocation;

require_once __DIR__.'/helpers.php';

/*
 * Manual Locations lifecycle browser test — runs against the real assembled core app.
 * Walks a missing location end to end and screenshots each stage: it shows as
 * "Unknown Structure" in the assets view, a submitted suggestion is reviewed and accepted on
 * the manage page, and the assets view then shows the accepted name. The "add suggestion" step
 * is seeded (the modal's solar-system field is a live-ESI autosuggest, not drivable offline);
 * the review/accept and both assets states are exercised through the UI.
 */

uses(RefreshDatabase::class);

it('walks a missing location from unknown → suggested → accepted → shown in assets', function (string $device) {
    $character = actingAsCharacter();
    $user = userOfCharacter($character->character_id);
    $user->givePermissionTo(Permission::findOrCreate('manage manual locations'));

    // An asset sitting at an unresolved location: a universe_locations row with no locatable.
    $unknownId = 987654321;
    Location::factory()->create(['location_id' => $unknownId]);
    makeCharacterAsset($character, $unknownId, $unknownId);

    // 1) In assets the location shows as an unknown structure (LocationName resolves the header
    //    on-demand via the get.manual_location endpoint).
    $page = deviceVisit($device, '/character/assets');
    $page->assertNoSmoke();
    $page->waitForText('Character Assets');
    assetTextVisible($page, "Unknown Structure ({$unknownId})");
    snap($page, "manual-locations-lifecycle-1-unknown-{$device}");

    // 2) A user submits a suggested name for it. Seeded rather than driven through the modal —
    //    its solar-system field is a live-ESI autosuggest that isn't drivable offline.
    ManualLocation::factory()->create([
        'location_id' => $unknownId,
        'user_id' => $user->id,
        'name' => 'Some Awesome Fortizar',
    ]);

    // 3) The admin reviews the suggestion on the manage page, then accepts it.
    $manage = deviceVisit($device, route('manage.manual_locations', absolute: false));
    $manage->assertNoSmoke();
    $manage->waitForText('Manual Locations');
    $manage->waitForText('This location could not be resolved'); // deferred list resolved
    // The radio option's label is "{system|'?'} - {name}"; no system is seeded, so match the
    // exact label text (pest-browser click targets an element by its exact text).
    $manage->waitForText('? - Some Awesome Fortizar');
    snap($manage, "manual-locations-lifecycle-2-review-{$device}");
    $manage->click('? - Some Awesome Fortizar');                  // select the radio option
    $manage->click('Save');                                       // accept it

    // 4) The assets view now shows the accepted name instead of "Unknown Structure".
    $resolved = deviceVisit($device, '/character/assets');
    $resolved->assertNoSmoke();
    $resolved->waitForText('Character Assets');
    assetTextVisible($resolved, 'Some Awesome Fortizar');
    snap($resolved, "manual-locations-lifecycle-3-resolved-{$device}");
})->with(['desktop', 'iphone']);
