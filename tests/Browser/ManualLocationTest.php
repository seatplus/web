<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Auth\Models\Permissions\Permission;
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
