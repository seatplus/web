<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Auth\Models\Permissions\Permission;

require_once __DIR__.'/helpers.php';

/*
 * Server-settings navigation browser test.
 *
 * The settings tab bar is static config (name/logo/route per tab) shared eagerly by
 * HandleInertiaRequests as `settingsNavigation` and read straight off the page props — there is
 * no post-mount axios/Ziggy fetch anymore. This proves the tabs render on first paint for a
 * superuser. The labels live in a desktop <nav> of <div>s AND a mobile <select> of <option>s, so
 * the nav is asserted via innerHTML (present on both viewports) rather than a visibility-sensitive
 * waitForText.
 */

uses(RefreshDatabase::class);

it('renders the server-settings navigation from a shared prop', function (string $device) {
    $character = actingAsCharacter();
    userOfCharacter($character->character_id)->givePermissionTo(Permission::findOrCreate('superuser'));
    cache()->flush();

    $page = deviceVisit($device, route('server.settings', absolute: false));
    // No assertNoSmoke here: the settings page's HorizonStats widget refreshes the `queueStats`
    // prop via an Inertia partial reload on mount, which resolves QueueStatsService (Horizon's
    // repositories) and can error under the test's Queue::fake. Unrelated to the navigation this
    // test covers.
    $page->waitForText('Available Settings');

    // Every configured tab rendered from the shared prop — no fetch required.
    $page->assertScript("document.body.innerHTML.includes('User List') && document.body.innerHTML.includes('SSO Setting') && document.body.innerHTML.includes('Schedules')");

    snap($page, "settings-navigation-{$device}");
})->with(['desktop', 'iphone']);
