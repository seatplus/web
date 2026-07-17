<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Models\Recruitment\Enlistment;

/*
 * Dashboard "Job Postings" browser tests — run against the real assembled core app.
 *
 * The dashboard (/home) lists open corporation enlistments in a "Job Postings" section.
 * That list is an Inertia *deferred* prop (openEnlistments): it is not part of the initial
 * HTML, so the Enlistments component renders a pulsing skeleton first and swaps in the
 * corporation cards once the follow-up partial reload resolves. These tests assert the
 * cards render after the deferred load. Provisioning via actingAsCharacter() (core
 * tests/Pest.php).
 */

uses(RefreshDatabase::class);

if (! function_exists('deviceVisit')) {
    /**
     * Visit $url on the given viewport ("desktop" or "iphone"). Browser tests run in the core app,
     * whose tests/Pest.php is not overlaid, so this helper is defined here (guarded) alongside the
     * suite's other function_exists helpers rather than in tests/Pest.php.
     */
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
    /**
     * Settle before screenshotting: flip lazy EVE-image portraits/logos to eager so off-screen
     * (full-page) images fetch, wait for the network to go idle, then capture — so screenshots show
     * resolved images instead of loading placeholders. Best-effort: a slow/absent image won't fail.
     */
    function snap($page, string $name): void
    {
        $page->script("document.querySelectorAll('img').forEach((i) => { i.loading = 'eager'; });");
        $page->waitForEvent('networkidle');
        $page->screenshot(true, $name);
    }
}

if (! function_exists('openEnlistmentForCorporation')) {
    /**
     * Open a real corporation for recruitment so the dashboard's "Job Postings" section has an
     * entry to render. A real corporation id means images.evetech.net serves a real logo in the
     * screenshot instead of the generic CDN placeholder. Returns the enlisting corporation.
     */
    function openEnlistmentForCorporation(string $type = 'user'): CorporationInfo
    {
        $corporation = CorporationInfo::factory()->create([
            'corporation_id' => 98008630,
            'name' => 'Thunderwaffe',
        ]);

        Enlistment::query()->create([
            'corporation_id' => $corporation->corporation_id,
            'type' => $type,
        ]);

        return $corporation;
    }
}

it('renders open enlistments in the dashboard job postings section after the deferred load', function (string $device) {
    actingAsCharacter();
    $corporation = openEnlistmentForCorporation();

    $page = deviceVisit($device, '/home');
    $page->assertNoSmoke();

    // The dashboard shell renders immediately (characters are a regular prop)...
    $page->waitForText('Characters');

    // ...while "Job Postings" belongs to the deferred openEnlistments prop: it is absent from the
    // initial HTML (a pulsing skeleton shows instead) and only appears once the partial reload
    // resolves. Waiting for it therefore proves the deferred load completed and the cards rendered.
    $page->waitForText('Job Postings');
    $page->assertSee($corporation->name);

    snap($page, "dashboard-enlistments-{$device}");

    test()->assertAuthenticated();
})->with(['desktop', 'iphone']);

it('wires the enlisting corporation logo to the EVE image server', function (string $device) {
    actingAsCharacter();
    $corporation = openEnlistmentForCorporation();

    $page = deviceVisit($device, '/home');
    $page->assertNoSmoke();

    // The card only mounts after the deferred openEnlistments prop resolves.
    $page->waitForText('Job Postings');
    $page->wait(1);

    // The enlistment card renders the corporation logo via EveImage against CCP's image server.
    $page->assertAttributeContains(
        "img[src*='corporations/{$corporation->corporation_id}/logo']",
        'src',
        "images.evetech.net/corporations/{$corporation->corporation_id}/logo",
    );

    snap($page, "dashboard-enlistment-logo-{$device}");
})->with(['desktop', 'iphone']);
