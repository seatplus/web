<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;

require_once __DIR__.'/helpers.php';

/*
 * Character assets browser tests — run against the real assembled core app.
 *
 * Covers the modernized assets view: the page-level Inertia <InfiniteScroll> over the
 * `assets` scroll prop (locations paginated with pageName 'assets'), the on-demand contents
 * modal (X-Modal fetch), and the shareable character.item deep link that renders the full
 * ItemDetails page on direct visit. Assets nest up to three levels (capital ship → freighter
 * → container → cargo); every child's location_id is its parent's item_id and every descendant
 * carries the top location's root_location_id (the flat descendant filter). A user always sees
 * their OWN characters' assets, so no permission is granted. Provisioning comes from the suite
 * helper actingAsCharacter() (core tests/Pest.php).
 */

uses(RefreshDatabase::class);

it('merges the next locations page in on scroll', function (string $device) {
    $character = actingAsCharacter();

    // >1 paginator page of locations (15/page) so the `assets` scroll prop has a next page.
    collect(range(1, 20))->each(function () use ($character) {
        $location = Location::factory()->for(Station::factory(), 'locatable')->create();
        makeCharacterAsset($character, $location->location_id, $location->location_id);
    });

    $rows = '#assets-body > *';

    $page = deviceVisit($device, '/character/assets');
    $page->assertNoSmoke();
    $page->waitForText('Character Assets');

    // The first page of location cards comes straight from the scroll prop, so it is present
    // immediately (no async needed to exist).
    $before = (int) $page->script("document.querySelectorAll('{$rows}').length");
    expect($before)->toBeGreaterThan(0);

    // The list scrolls inside the layout's <main class="… overflow-y-auto">, not the window.
    // Re-scroll it to the bottom on every poll (comma expression) so InfiniteScroll's end
    // trigger fires; passes once the next page has merged in.
    $page->assertScript("(document.getElementById('assets-body').closest('.overflow-y-auto').scrollTo(0, 1e6), document.querySelectorAll('{$rows}').length > {$before})");

    snap($page, "character-assets-infinite-scroll-{$device}");
})->with(['desktop', 'iphone']);

it('drills three levels deep via the shareable item deep link', function (string $device) {
    $character = actingAsCharacter();
    ['capital' => $capital, 'freighter' => $freighter, 'container' => $container, 'cargo' => $cargo] = makeNestedAssetChain($character);

    $page = deviceVisit($device, '/character/assets');
    $page->assertNoSmoke();
    $page->waitForText('Character Assets');

    // The top-level capital ship renders in its location list with a contents affordance:
    // the chevron links to the shareable character.item URL.
    assetTextVisible($page, $capital->name);
    $page->assertPresent("a[href*='/item/{$capital->item_id}']");

    // Follow that shareable link down each containment level. The item() page renders one
    // level of contents, so each parent's ItemDetails shows the next-deeper asset.
    $level2 = deviceVisit($device, route('character.item', ['character_id' => $character->character_id, 'item_id' => $capital->item_id], false));
    $level2->assertNoSmoke();
    $level2->waitForText($freighter->name);

    $level3 = deviceVisit($device, route('character.item', ['character_id' => $character->character_id, 'item_id' => $freighter->item_id], false));
    $level3->assertNoSmoke();
    $level3->waitForText($container->name);

    // Three layers of nesting: the container's page shows the cargo inside it.
    $level4 = deviceVisit($device, route('character.item', ['character_id' => $character->character_id, 'item_id' => $container->item_id], false));
    $level4->assertNoSmoke();
    $level4->waitForText($cargo->name);

    snap($level4, "character-assets-deeplink-depth-three-{$device}");
})->with(['desktop', 'iphone']);

it('opens a container’s contents in a modal on click', function (string $device) {
    $character = actingAsCharacter();
    ['capital' => $capital, 'freighter' => $freighter] = makeNestedAssetChain($character);

    $page = deviceVisit($device, '/character/assets');
    $page->assertNoSmoke();
    $page->waitForText('Character Assets');
    assetTextVisible($page, $capital->name);

    // Clicking the capital's contents affordance opens the modal and fetches its one level of
    // contents on demand (X-Modal) — the freighter appears without a full page navigation.
    $page->click("a[href*='/item/{$capital->item_id}']");
    $page->waitForText($freighter->name);

    snap($page, "character-assets-contents-modal-{$device}");
})->with(['desktop', 'iphone']);

it('surfaces asset safety as its own location', function (string $device) {
    $character = actingAsCharacter();

    // Asset-safety items carry the sentinel location id; the action prepends a synthetic
    // location for them on page 1 (uncommon in practice, hence a dedicated edge-case check).
    $safety = makeCharacterAsset($character, Asset::ASSET_SAFETY, Asset::ASSET_SAFETY, ['location_flag' => 'AssetSafety']);

    $page = deviceVisit($device, '/character/assets');
    $page->assertNoSmoke();
    $page->waitForText('Character Assets');
    assetTextVisible($page, $safety->name);

    snap($page, "character-assets-asset-safety-{$device}");
})->with(['desktop', 'iphone']);

it('narrows a location to only the top-level items that match the search at any depth', function (string $device) {
    $character = actingAsCharacter();

    $location = Location::factory()->for(Station::factory(), 'locatable')->create();
    $root = $location->location_id;

    // A top-level container ("Praetor Bay") that HOLDS a nested asset named to match the search…
    $container = makeCharacterAsset($character, $root, $root, ['name' => 'Praetor Bay']);
    $container->update(['root_item_id' => $container->item_id]);
    $nested = makeCharacterAsset($character, $container->item_id, $root, ['name' => 'Herpaderp Ibis', 'location_flag' => 'Cargo']);
    $nested->update(['root_item_id' => $container->item_id]);

    // …and a separate top-level item that does NOT match.
    $other = makeCharacterAsset($character, $root, $root, ['name' => 'Quafe Crate']);
    $other->update(['root_item_id' => $other->item_id]);

    $page = deviceVisit($device, '/character/assets');
    $page->assertNoSmoke();
    $page->waitForText('Character Assets');

    // Unfiltered, the location lazy-loads both of its top-level items (scroll to trigger).
    assetTextVisible($page, 'Praetor Bay');
    assetTextVisible($page, 'Quafe Crate');

    // Searching "herp" matches the nested asset ("Herpaderp Ibis") and rolls it up to its top-level
    // container via root_item_id: only "Praetor Bay" survives, the unrelated top-level item drops out.
    // Drives the shell re-query AND the per-location filtered-top-level refetch end-to-end.
    $page->type('search', 'herp');
    // The filtered re-query rebuilds the location list, which lazy-loads on scroll again.
    assertAssetsScript($page, "document.body.innerText.includes('Praetor Bay') && !document.body.innerText.includes('Quafe Crate')");
    $page->assertNoSmoke();

    snap($page, "character-assets-search-{$device}");
})->with(['desktop', 'iphone']);

it('renders a location for every character the user owns', function (string $device) {
    $mainCharacter = actingAsCharacter();
    $secondCharacter = attachOwnedCharacter($mainCharacter);

    // Each owned character holds assets in its own station. The view aggregates over every
    // character the user owns (getCharacterIds), so BOTH locations must render — a single-character
    // scope would show only the main's.
    $locationA = Location::factory()->for(Station::factory(), 'locatable')->create();
    $locationB = Location::factory()->for(Station::factory(), 'locatable')->create();
    makeCharacterAsset($mainCharacter, $locationA->location_id, $locationA->location_id);
    makeCharacterAsset($secondCharacter, $locationB->location_id, $locationB->location_id);

    $page = deviceVisit($device, '/character/assets');
    $page->assertNoSmoke();
    $page->waitForText('Character Assets');

    // Location cards are shells from the scroll prop, so their "system - station" header renders
    // immediately (before the per-location lazy load). Assert both stations are present.
    $page->waitForText($locationA->locatable->name);
    $page->waitForText($locationB->locatable->name);

    snap($page, "character-assets-multiple-characters-{$device}");
})->with(['desktop', 'iphone']);

it('filters the location list by region', function (string $device) {
    $character = actingAsCharacter();

    $locationA = Location::factory()->for(Station::factory(), 'locatable')->create();
    $locationB = Location::factory()->for(Station::factory(), 'locatable')->create();
    makeCharacterAsset($character, $locationA->location_id, $locationA->location_id);
    makeCharacterAsset($character, $locationB->location_id, $locationB->location_id);

    $regionA = $locationA->locatable->system->region->name;
    $stationA = $locationA->locatable->name;
    $stationB = $locationB->locatable->name;

    $page = deviceVisit($device, '/character/assets');
    $page->assertNoSmoke();
    $page->waitForText('Character Assets');
    $page->waitForText($stationA);
    $page->waitForText($stationB);

    // Pick location A's region in the combobox (mirrors the wallet-filter pattern: type to filter
    // the options, then click the match). The region name only appears as a combobox option, never
    // in a card header, so it is an unambiguous click target. The shell list re-queries and drops B.
    $page->type('region-filter', $regionA);
    $page->waitForText($regionA);
    $page->click($regionA);

    $page->assertScript(sprintf(
        '(document.body.innerText.includes(%s) && !document.body.innerText.includes(%s))',
        json_encode($stationA),
        json_encode($stationB),
    ));
    $page->assertNoSmoke();

    snap($page, "character-assets-region-filter-{$device}");
})->with(['desktop', 'iphone']);
