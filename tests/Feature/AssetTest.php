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
