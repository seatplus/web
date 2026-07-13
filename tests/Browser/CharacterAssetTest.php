<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Eveapi\Models\Universe\Type;

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

if (! function_exists('makeCharacterAsset')) {
    /**
     * Create one named asset owned by $character at $locationId (rooted at $rootLocationId).
     * It gets a real published Type backed by a real Group so the ItemDetails page — whose
     * LocationSlot dereferences asset.type.name / asset.type.group.name without a null-guard —
     * renders cleanly. Guarded so each Browser file can define it standalone.
     *
     * @param  array<string, mixed>  $overrides
     */
    function makeCharacterAsset(CharacterInfo $character, int $locationId, int $rootLocationId, array $overrides = []): Asset
    {
        $type = Type::factory()->create(['group_id' => Group::factory()->create()->group_id]);

        return Asset::factory()->withName()->create(array_merge([
            'assetable_id' => $character->character_id,
            'assetable_type' => CharacterInfo::class,
            'location_id' => $locationId,
            'root_location_id' => $rootLocationId,
            'location_flag' => 'Hangar',
            'type_id' => $type->type_id,
        ], $overrides));
    }
}

if (! function_exists('makeNestedAssetChain')) {
    /**
     * Seed a three-level nesting rooted in a station location:
     * capital ship → freighter → container → cargo (each child.location_id = parent.item_id).
     * Returns [location, capital, freighter, container, cargo].
     *
     * @return array<string, mixed>
     */
    function makeNestedAssetChain(CharacterInfo $character): array
    {
        $location = Location::factory()->for(Station::factory(), 'locatable')->create();
        $root = $location->location_id;

        $capital = makeCharacterAsset($character, $root, $root);
        $freighter = makeCharacterAsset($character, $capital->item_id, $root, ['location_flag' => 'ShipHangar']);
        $container = makeCharacterAsset($character, $freighter->item_id, $root, ['location_flag' => 'Cargo']);
        $cargo = makeCharacterAsset($character, $container->item_id, $root, ['location_flag' => 'Cargo']);

        return compact('location', 'capital', 'freighter', 'container', 'cargo');
    }
}

it('merges the next locations page in on scroll', function () {
    $character = actingAsCharacter();

    // >1 paginator page of locations (15/page) so the `assets` scroll prop has a next page.
    collect(range(1, 20))->each(function () use ($character) {
        $location = Location::factory()->for(Station::factory(), 'locatable')->create();
        makeCharacterAsset($character, $location->location_id, $location->location_id);
    });

    $rows = '#assets-body > *';

    $page = visit('/character/assets');
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

    $page->screenshot(true, 'character-assets-infinite-scroll');
});

it('drills three levels deep via the shareable item deep link', function () {
    $character = actingAsCharacter();
    ['capital' => $capital, 'freighter' => $freighter, 'container' => $container, 'cargo' => $cargo] = makeNestedAssetChain($character);

    $page = visit('/character/assets');
    $page->assertNoSmoke();
    $page->waitForText('Character Assets');

    // The top-level capital ship renders in its location list with a contents affordance:
    // the chevron links to the shareable character.item URL.
    $page->waitForText($capital->name);
    $page->assertPresent("a[href*='/item/{$capital->item_id}']");

    // Follow that shareable link down each containment level. The item() page renders one
    // level of contents, so each parent's ItemDetails shows the next-deeper asset.
    $level2 = visit(route('character.item', ['character_id' => $character->character_id, 'item_id' => $capital->item_id], false));
    $level2->assertNoSmoke();
    $level2->waitForText($freighter->name);

    $level3 = visit(route('character.item', ['character_id' => $character->character_id, 'item_id' => $freighter->item_id], false));
    $level3->assertNoSmoke();
    $level3->waitForText($container->name);

    // Three layers of nesting: the container's page shows the cargo inside it.
    $level4 = visit(route('character.item', ['character_id' => $character->character_id, 'item_id' => $container->item_id], false));
    $level4->assertNoSmoke();
    $level4->waitForText($cargo->name);

    $level4->screenshot(true, 'character-assets-deeplink-depth-three');
});

it('opens a container’s contents in a modal on click', function () {
    $character = actingAsCharacter();
    ['capital' => $capital, 'freighter' => $freighter] = makeNestedAssetChain($character);

    $page = visit('/character/assets');
    $page->assertNoSmoke();
    $page->waitForText('Character Assets');
    $page->waitForText($capital->name);

    // Clicking the capital's contents affordance opens the modal and fetches its one level of
    // contents on demand (X-Modal) — the freighter appears without a full page navigation.
    $page->click("a[href*='/item/{$capital->item_id}']");
    $page->waitForText($freighter->name);

    $page->screenshot(true, 'character-assets-contents-modal');
});

it('surfaces asset safety as its own location', function () {
    $character = actingAsCharacter();

    // Asset-safety items carry the sentinel location id; the action prepends a synthetic
    // location for them on page 1 (uncommon in practice, hence a dedicated edge-case check).
    $safety = makeCharacterAsset($character, Asset::ASSET_SAFETY, Asset::ASSET_SAFETY, ['location_flag' => 'AssetSafety']);

    $page = visit('/character/assets');
    $page->assertNoSmoke();
    $page->waitForText('Character Assets');
    $page->waitForText($safety->name);

    $page->screenshot(true, 'character-assets-asset-safety');
});
