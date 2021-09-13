<?php


use Illuminate\Support\Facades\Bus;
use Inertia\Testing\Assert;
use Illuminate\Support\Facades\Queue;
use Seatplus\Eveapi\Jobs\Universe\ResolveUniverseSystemBySystemIdJob;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Web\Models\ManualLocation;
use Seatplus\Web\Tests\TestCase;


beforeEach(function () {
    Queue::fake();
});

it('resolves unknown location', function () {
    $manual_loaction = ManualLocation::factory()->make();

    $expected_name = sprintf('Unknown Structure (%s)', $manual_loaction->location_id);

    test()->actingAs(test()->test_user)
        ->get(route('get.manual_location', $manual_loaction->location_id))
        ->assertOk()
        ->assertJson(['name' => $expected_name]);

});

test('one can submit suggestion', function () {

    Bus::fake();

    $manual_loaction = ManualLocation::factory()->make();

    $response = test()->actingAs(test()->test_user)
        ->post(route('post.manual_location'), [
            'name' => $manual_loaction->name,
            'location_id' => $manual_loaction->location_id,
            'solar_system_id' => $manual_loaction->solar_system_id
        ])->assertRedirect();

    Bus::dispatchedAfterResponse(ResolveUniverseSystemBySystemIdJob::class);
});

test('one get own suggestion', function () {
    $manual_loaction = ManualLocation::factory()->create([
        'user_id' => test()->test_user->id
    ]);

    $response = test()->actingAs(test()->test_user)
        ->get(route('get.manual_location', $manual_loaction->location_id))
        ->assertOk()
        ->assertJson(['name' => $manual_loaction->name]);
});

test('one get suggestion of other user', function () {
    ManualLocation::factory()->count(5)->create([
        'location_id' => 12345,
        'created_at' => carbon()->subDay()
    ]);

    $manual_loaction = ManualLocation::factory()->create([
        'location_id' => 12345,
    ]);

    test()->actingAs(test()->test_user)
        ->get(route('get.manual_location', 12345))
        ->assertOk()
        ->assertJson(['name' => $manual_loaction->name]);
});

test('admin can accept suggestion', function () {
    ManualLocation::factory()->count(4)->create([
        'location_id' => 12345,
        'created_at' => carbon()->subDay()
    ]);

    $manual_location = ManualLocation::factory()->create([
        'location_id' => 12345,
    ]);

    test()->givePermissionsToTestUser(['manage manual locations']);

    // first visit Manage view
    $response = test()->actingAs(test()->test_user)
        ->get(route('manage.manual_locations'))
        ->assertOk();

    $response->assertInertia(fn(Assert $page) => $page->component('Configuration/ManualLocations/ManualLocation'));

    // load suggestions
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.manuel_locations.suggestions'))
        ->assertOk();

    // check that there are 5 suggestions
    expect(json_decode($response->content())->data)->toHaveCount(5);

    // Make sure there is no suggestion in universe_locations
    test()->assertNull(Location::firstWhere(['location_id' =>12345]));

    // accept one
    $response = test()->actingAs(test()->test_user)
        ->post(route('get.manuel_locations.suggestions'), [
            'id' => $manual_location->id,
            'location_id' => $manual_location->location_id
        ])
        ->assertRedirect(route('manage.manual_locations'));

    // Make sure there is one suggestion in universe_locations
    test()->assertCount(1, Location::where('location_id',12345)->get());

    // check that there there is only one left after accepting
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.manuel_locations.suggestions'))
        ->assertOk();
    expect(json_decode($response->content())->data)->toHaveCount(1);

});

test('one get accepted suggestion', function () {
    ManualLocation::factory()->count(4)->create([
        'location_id' => 12345,
        'created_at' => carbon()->subDay()
    ]);

    $manual_location = ManualLocation::factory()->create([
        'location_id' => 12345,
    ]);

    test()->givePermissionsToTestUser(['manage manual locations']);

    // Make sure there is no suggestion in universe_locations
    test()->assertNull(Location::firstWhere(['location_id' =>12345]));

    // accept one
    $response = test()->actingAs(test()->test_user)
        ->post(route('get.manuel_locations.suggestions'), [
            'id' => $manual_location->id,
            'location_id' => $manual_location->location_id
        ])
        ->assertRedirect(route('manage.manual_locations'));

    // Make sure there is one suggestion in universe_locations
    test()->assertCount(1, Location::where('location_id',12345)->get());

    // Lookup name
    test()->actingAs(test()->test_user)
        ->get(route('get.manual_location', $manual_location->location_id))
        ->assertOk()
        ->assertJson(['name' => $manual_location->name]);

});

test('if location is resolved via jobs delete manual suggestions', function () {

    $manual_location = ManualLocation::factory()->create();

    $station = Station::factory()->create([
        'station_id' => $manual_location->location_id
    ]);

    $location = Location::factory()->create([
        'location_id' => $manual_location->location_id,
        'locatable_id' => $manual_location->location_id,
        'locatable_type' => Station::class
    ]);

    test()->givePermissionsToTestUser(['manage manual locations']);

    // load suggestions
    $response = test()->actingAs(test()->test_user)
        ->get(route('get.manuel_locations.suggestions'))
        ->assertOk();

    // check that there are no suggestions
    expect(json_decode($response->content())->data)->toHaveCount(0);
    expect(ManualLocation::all())->toBeEmpty();

});

test('if location does not have system dispatch job', function () {
    $manual_location = ManualLocation::factory()->create();

    // Lookup name
    test()->actingAs(test()->test_user)
        ->get(route('get.manual_location', $manual_location->location_id))
        ->assertOk()
        ->assertJson(['name' => $manual_location->name]);

    Queue::assertPushedOn('low', ResolveUniverseSystemBySystemIdJob::class);
});
