<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Web\Models\Recruitment\Enlistment;

/*
 * Recruitment configuration browser test.
 *
 * The configuration page (Corporation/Recruitment/Configuration/Index) is corporation-scoped:
 * EnlistmentsController@edit renders it behind CheckAuthorization:'can open or close corporations
 * for recruitment,director'. Access is granted here by the in-game Director corp role
 * (giveCorporationRole) — not superuser — which CanUserService accepts for the ,director gate,
 * mirroring a real senior-HR director opening their corporation's recruitment settings.
 *
 * An Enlistment record must exist for the corporation for @edit to hydrate the page; without it the
 * `enlistment` prop is null and the page cannot render the (edit-mode) config card. Provisioning
 * helpers (actingAsCharacter, giveCorporationRole) live in the core app's tests/Browser/Pest.php —
 * browser tests run against the assembled core, whose Pest.php is not overlaid here, so the two
 * viewport/screenshot helpers below are defined guarded alongside the suite's other tests.
 */

uses(RefreshDatabase::class);

if (! function_exists('deviceVisit')) {
    function deviceVisit(string $device, string $url, array $options = []): mixed
    {
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

if (! function_exists('snap')) {
    function snap($page, string $name): void
    {
        $page->script("document.querySelectorAll('img').forEach((i) => { i.loading = 'eager'; });");
        $page->waitForEvent('networkidle');
        $page->screenshot(true, $name);
    }
}

it('a corp admin sees the recruitment configuration', function (string $device) {
    $character = actingAsCharacter();
    $corporationId = $character->corporation->corporation_id;

    // Director corp role → satisfies the ,director branch of the recruitment gate (no superuser).
    giveCorporationRole($character);

    // The open enlistment @edit hydrates the page from.
    Enlistment::create([
        'corporation_id' => $corporationId,
        'type' => 'user',
        'steps' => 'First interview; Second interview',
    ]);

    $page = deviceVisit($device, "/corporation/recruitment/{$corporationId}");
    $page->assertNoSmoke();

    // Page header + the enlistment config card (edit mode: distinctive "Review Process Steps" field).
    $page->waitForText('Corporation Enlistment');
    $page->assertSee('Enlistment');
    $page->assertSee('Review Process Steps');

    // Region/System filter card and the items watchlist card both render.
    $page->assertSee('Region or System Filter');
    $page->assertSee('Items Watchlist');

    // A config form is present: the review-process-steps input and the save actions render.
    $page->assertScript("!!document.querySelector('#steps')");
    $page->assertSee('Save');

    snap($page, "recruitment-config-{$device}");
})->with(['desktop', 'iphone']);
