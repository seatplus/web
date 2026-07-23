<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Web\Models\ManualLocation;

require_once __DIR__.'/helpers.php';

/*
 * Manual Locations lifecycle browser test — runs against the real assembled core app.
 * Walks a missing location end to end and screenshots each stage: it shows as
 * "Unknown Structure" in the assets view; a user opens the "Add location information" modal to
 * submit a name; an admin reviews and accepts the suggestion on the manage page; and the assets
 * view then shows the accepted name. The submission itself is seeded rather than posted through
 * the modal (its solar-system field is a live-ESI autosuggest that isn't drivable offline) — but
 * the modal, the review/accept, and both assets states are exercised through the UI.
 */

uses(RefreshDatabase::class);

it('walks a missing location from unknown → add → accepted → shown in assets', function (string $device) {
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

    // 2) Clicking "Add location information" opens the submission modal (teleported to
    //    #destination). Confirm the header button rendered (and let the list scroll), then fire
    //    its native click — the button sits in the assets list's nested scroll container, which
    //    click() won't auto-scroll to; a native .click() still runs Vue's @click handler.
    //    Merely opening the modal is safe to screenshot (its solar-system field only hits ESI on
    //    type). No assertNoSmoke here: that field's token probe can log under the faked queue/ESI.
    assetTextVisible($page, 'Add location information');
    $page->script("[...document.querySelectorAll('button')].find(b => b.textContent.includes('Add location information'))?.click()");
    $page->waitForText("Add location information for unknown structure ({$unknownId})");
    snap($page, "manual-locations-lifecycle-2-add-{$device}");

    // 3) A user submits a suggested name (seeded — the modal's ESI autosuggest isn't drivable
    //    offline). The admin then reviews and accepts it on the manage page.
    ManualLocation::factory()->create([
        'location_id' => $unknownId,
        'user_id' => $user->id,
        'name' => 'Some Awesome Fortizar',
    ]);

    $manage = deviceVisit($device, route('manage.manual_locations', absolute: false));
    $manage->assertNoSmoke();
    $manage->waitForText('Manual Locations');
    $manage->waitForText('This location could not be resolved'); // deferred list resolved
    // The radio option's label is "{system|'?'} - {name}"; no system is seeded, so match the
    // exact label text (pest-browser click targets an element by its exact text).
    $manage->waitForText('? - Some Awesome Fortizar');
    snap($manage, "manual-locations-lifecycle-3-review-{$device}");
    $manage->click('? - Some Awesome Fortizar');                  // select the radio option
    $manage->click('Save');                                       // accept it

    // 4) The assets view now shows the accepted name instead of "Unknown Structure".
    $resolved = deviceVisit($device, '/character/assets');
    $resolved->assertNoSmoke();
    $resolved->waitForText('Character Assets');
    assetTextVisible($resolved, 'Some Awesome Fortizar');
    snap($resolved, "manual-locations-lifecycle-4-resolved-{$device}");
})->with(['desktop', 'iphone']);
