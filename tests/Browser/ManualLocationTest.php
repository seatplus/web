<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Web\Models\ManualLocation;

require_once __DIR__.'/helpers.php';

/*
 * Manual Locations browser test — runs against the real assembled core app.
 * The index page ships its suggestion list as an `Inertia::defer`red `data` prop
 * rendered through <Deferred>, so wait for the loaded content rather than asserting
 * immediately.
 */

uses(RefreshDatabase::class);

it('lists manual-location suggestions for an authorized user', function (string $device) {
    $character = actingAsCharacter();
    userOfCharacter($character->character_id)
        ->givePermissionTo(Permission::findOrCreate('manage manual locations'));

    // Two competing suggestions for one unresolved location (each by its own user).
    ManualLocation::factory()->count(2)->create(['location_id' => 987654321]);

    $page = deviceVisit($device, route('manage.manual_locations', absolute: false));

    $page->assertNoSmoke();
    $page->waitForText('Manual Locations');                     // page header
    $page->waitForText('This location could not be resolved');  // deferred suggestion group rendered
    $page->assertSee('Save');                                   // the accept action is present

    snap($page, "manual-locations-{$device}");
})->with(['desktop', 'iphone']);

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

    // 2) A user submits a suggested name for it. Seeded rather than driven through the modal —
    //    its solar-system field is a live-ESI autosuggest that isn't drivable offline.
    ManualLocation::factory()->create([
        'location_id' => $unknownId,
        'user_id' => $user->id,
        'name' => 'Some Awesome Fortizar',
    ]);

    // 3) The admin accepts the suggestion on the manage page.
    $manage = deviceVisit($device, route('manage.manual_locations', absolute: false));
    $manage->assertNoSmoke();
    $manage->waitForText('Manual Locations');
    $manage->waitForText('This location could not be resolved'); // deferred list resolved
    $manage->waitForText('Some Awesome Fortizar');                // the suggestion option
    $manage->click('Some Awesome Fortizar');                      // select the radio option
    $manage->click('Save');                                       // accept it

    // 4) The assets view now shows the accepted name instead of "Unknown Structure".
    $resolved = deviceVisit($device, '/character/assets');
    $resolved->assertNoSmoke();
    $resolved->waitForText('Character Assets');
    assetTextVisible($resolved, 'Some Awesome Fortizar');

    snap($resolved, "manual-locations-lifecycle-{$device}");
})->with(['desktop', 'iphone']);
