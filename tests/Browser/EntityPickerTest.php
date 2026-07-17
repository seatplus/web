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
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Character\CharacterInfo;

uses(RefreshDatabase::class);

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

// The picker SlideOver teleports into <div id="destination"> (app.blade.php), outside the page's
// own content. The assets page also renders a card per character with the same name, so page-wide
// assertSee can't tell the picker apart — scope assertions to #destination.
if (! function_exists('pickerSees')) {
    function pickerSees(string $text): string
    {
        return "(document.querySelector('#destination')?.innerText ?? '').includes(".json_encode($text).')';
    }
}

/*
 * Entity-picker SlideOver ("Select character") — the axios/Ziggy-free rewrite.
 *
 * The picker lists the characters the user may view (GetAffiliatedCharactersController, fetched via
 * the native-fetch getJson helper + Wayfinder), lets you tick a subset, and on close navigates the
 * page with `character_ids[]` so the list filters to the selection. This asserts the whole
 * round-trip works without Ziggy/axios: the picker lists both affiliated characters, selecting one
 * writes it to the URL query the page controller reads, and the "has selection" indicator lights up
 * after the reload.
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

    // Open the picker: the SlideOver teleports into #destination and getJson-loads both characters.
    $page->click('Select Character');
    $page->waitForText('Select character');
    $page->assertScript(pickerSees($owned->name));
    $page->assertScript(pickerSees($affiliated->name));

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
