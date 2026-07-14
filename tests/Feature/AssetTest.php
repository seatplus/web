<?php

use Illuminate\Support\Collection;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Eveapi\Models\Universe\Type;
use Seatplus\Web\Http\Actions\Character\Asset\GetCharacterAssetLocationAction;

// Run the location action for the test character with the given filters and return its locations.
function assetLocations(array $filters = []): Collection
{
    $validated = array_merge(['character_ids' => [test()->test_character->character_id]], $filters);

    return collect((new GetCharacterAssetLocationAction)->execute($validated)->items());
}

test('is protected by authentication', function () {
    test()->followingRedirects()
        ->get(route('character.assets'))
        ->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
});

test('renders the assets page with an assets scroll prop', function () {
    Asset::factory()->create([
        'assetable_id' => test()->test_character->character_id,
        'assetable_type' => CharacterInfo::class,
    ]);

    test()->actingAs(test()->test_user)
        ->get(route('character.assets'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Character/Assets')
                ->has('dispatchTransferObject')
                ->has('characterIds')
                ->has('assets')
        );
});

it('has list affiliated character list route', function () {
    Asset::factory()->create([
        'assetable_id' => test()->test_character->character_id,
        'assetable_type' => CharacterInfo::class,
    ]);

    test()->actingAs(test()->test_user)
        ->get(route('get.affiliated.characters', [
            'permission' => 'assets',
            'search' => substr((string) test()->test_character->name, 5),
        ]))
        ->assertOk();
});

test('the action paginates locations and honours only_unknown_locations', function () {
    $unknown = Location::factory()->create();
    $known = Location::factory()->for(Station::factory(), 'locatable')->create();

    foreach ([$known, $unknown] as $location) {
        Asset::factory()->create([
            'assetable_id' => test()->test_character->character_id,
            'location_id' => $location->location_id,
            'root_location_id' => $location->location_id,
            'location_flag' => 'Hangar',
        ]);
    }

    expect(assetLocations())->toHaveCount(2)
        ->and(assetLocations(['only_unknown_locations' => true]))->toHaveCount(1);
});

test('the action filters locations by a matching asset at any nesting depth', function () {
    $location = Location::factory()->for(Station::factory(), 'locatable')->create();

    // capital (depth 1) → freighter (depth 2) → container (depth 3), all rooted in $location.
    $asset = Asset::factory()->create([
        'assetable_id' => test()->test_character->character_id,
        'location_id' => $location->location_id,
        'root_location_id' => $location->location_id,
        'location_flag' => 'Hangar',
    ]);
    $content = Asset::factory()->create([
        'assetable_id' => test()->test_character->character_id,
        'location_id' => $asset->item_id,
        'root_location_id' => $location->location_id,
        'group_id' => Group::factory(),
    ]);
    $contentContent = Asset::factory()->create([
        'assetable_id' => test()->test_character->character_id,
        'location_id' => $content->item_id,
        'root_location_id' => $location->location_id,
        'type_id' => Type::factory(),
        'group_id' => Group::factory(),
        'category_id' => Category::factory(),
    ]);

    $filters = [
        'systems' => [$location->locatable->system->system_id],
        'regions' => [$location->locatable->system->region->region_id],
        'types' => [$asset->type_id],                      // matches at depth 1
        'groups' => [$content->group_id],                  // matches at depth 2
        'categories' => [$contentContent->category_id],    // matches at depth 3
    ];

    foreach ($filters as $key => $value) {
        expect(assetLocations([$key => $value]))->toHaveCount(1);
    }
});

test('the action text search is case-insensitive', function () {
    $location = Location::factory()->for(Station::factory(), 'locatable')->create();

    // name_normalized is generated as PascalCase ("SodiaTradealinasIbis"); a lower-cased
    // search term must still match it (regression: `like` was case-sensitive on Postgres).
    Asset::factory()->create([
        'assetable_id' => test()->test_character->character_id,
        'location_id' => $location->location_id,
        'root_location_id' => $location->location_id,
        'location_flag' => 'Hangar',
        'name' => 'Sodia Tradealinas Ibis',
    ]);

    expect(assetLocations(['search' => 'sodia']))->toHaveCount(1);
    expect(assetLocations(['search' => 'SODIA']))->toHaveCount(1);
    expect(assetLocations(['search' => 'nomatch']))->toHaveCount(0);
});

test('item() returns the item plus one level of contents as JSON for the modal', function () {
    $container = Asset::factory()->create([
        'assetable_id' => test()->test_character->character_id,
        'assetable_type' => CharacterInfo::class,
    ]);
    $inside = Asset::factory()->create([
        'assetable_id' => test()->test_character->character_id,
        'assetable_type' => CharacterInfo::class,
        'location_id' => $container->item_id,
    ]);

    test()->actingAs(test()->test_user)
        ->withHeader('X-Modal', 'true')
        ->getJson(route('character.item', [
            'character_id' => test()->test_character->character_id,
            'item_id' => $container->item_id,
        ]))
        ->assertOk()
        ->assertJsonFragment(['item_id' => $inside->item_id]);
});

test('item() renders the ItemDetails page on a direct (shareable) visit', function () {
    $container = Asset::factory()->create([
        'assetable_id' => test()->test_character->character_id,
        'assetable_type' => CharacterInfo::class,
    ]);

    test()->actingAs(test()->test_user)
        ->get(route('character.item', [
            'character_id' => test()->test_character->character_id,
            'item_id' => $container->item_id,
        ]))
        ->assertInertia(fn (Assert $page) => $page->component('Character/ItemDetails')->has('item'));
});

// A location + a top-level container (100) holding a depth-2 matching asset (200), plus a
// separate non-matching top-level item (300). Returns the character id and the location.
function seedLocationWithNestedMatch(): array
{
    $characterId = test()->test_character->character_id;
    $location = Location::factory()->for(Station::factory(), 'locatable')->create();

    Asset::factory()->create([
        'item_id' => 100, 'assetable_id' => $characterId, 'assetable_type' => CharacterInfo::class,
        'location_id' => $location->location_id, 'root_location_id' => $location->location_id,
        'root_item_id' => 100, 'location_flag' => 'Hangar', 'name' => 'Container',
    ]);
    Asset::factory()->create([ // nested match — rolls up to top-level 100 via root_item_id
        'item_id' => 200, 'assetable_id' => $characterId, 'assetable_type' => CharacterInfo::class,
        'location_id' => 100, 'root_location_id' => $location->location_id,
        'root_item_id' => 100, 'name' => 'Sodia Ibis',
    ]);
    Asset::factory()->create([ // separate top-level item, no match
        'item_id' => 300, 'assetable_id' => $characterId, 'assetable_type' => CharacterInfo::class,
        'location_id' => $location->location_id, 'root_location_id' => $location->location_id,
        'root_item_id' => 300, 'location_flag' => 'Hangar', 'name' => 'Other',
    ]);

    return [$characterId, $location];
}

test('location() returns only the top-level items that match at any depth (filtered)', function () {
    [$characterId, $location] = seedLocationWithNestedMatch();

    test()->actingAs(test()->test_user)
        ->getJson(route('character.location', [
            'location_id' => $location->location_id,
            'character_ids' => [$characterId],
            'search' => 'sodia',
        ]))
        ->assertOk()
        ->assertJsonFragment(['item_id' => 100])   // top-level container that holds the match
        ->assertJsonMissing(['item_id' => 300])    // non-matching top-level excluded
        ->assertJsonPath('meta.total', 1);
});

test('location() returns the location direct children unfiltered', function () {
    [$characterId, $location] = seedLocationWithNestedMatch();

    test()->actingAs(test()->test_user)
        ->getJson(route('character.location', [
            'location_id' => $location->location_id,
            'character_ids' => [$characterId],
        ]))
        ->assertOk()
        // the two top-level items (100, 300) — not the nested 200
        ->assertJsonPath('meta.total', 2)
        ->assertJsonFragment(['item_id' => 100])
        ->assertJsonFragment(['item_id' => 300])
        ->assertJsonMissing(['item_id' => 200]);
});

test('location() returns nothing for a character the user is not authorised for', function () {
    $location = Location::factory()->for(Station::factory(), 'locatable')->create();
    $other = CharacterInfo::factory()->create(); // not owned/affiliated by the test user

    Asset::factory()->create([
        'item_id' => 999, 'assetable_id' => $other->character_id, 'assetable_type' => CharacterInfo::class,
        'location_id' => $location->location_id, 'root_location_id' => $location->location_id,
        'root_item_id' => 999, 'location_flag' => 'Hangar',
    ]);

    test()->actingAs(test()->test_user)
        ->getJson(route('character.location', [
            'location_id' => $location->location_id,
            'character_ids' => [$other->character_id],
        ]))
        ->assertOk()
        ->assertJsonPath('meta.total', 0); // requested id intersected out of the authorised set
});

test('index provides region/system filter options from the character locations', function () {
    $location = Location::factory()->for(Station::factory(), 'locatable')->create();
    Asset::factory()->create([
        'assetable_id' => test()->test_character->character_id,
        'assetable_type' => CharacterInfo::class,
        'location_id' => $location->location_id,
        'root_location_id' => $location->location_id,
        'location_flag' => 'Hangar',
    ]);

    test()->actingAs(test()->test_user)
        ->get(route('character.assets'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('filterOptions.regions', 1)
            ->has('filterOptions.systems', 1));
});
