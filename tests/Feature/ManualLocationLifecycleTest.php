<?php

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Jobs\Universe\ResolveUniverseSystemBySystemIdJob;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Web\Http\Resources\LocationRessource;
use Seatplus\Web\Models\ManualLocation;
use Seatplus\Web\Tests\TestCase;

beforeEach(function () {
    Queue::fake();
});

it('resolves unknown location', function () {
    $manual_loaction = ManualLocation::factory()->make();

    $expected_name = sprintf('Unknown Structure (%s)', $manual_loaction->location_id);

    $this->actingAs($this->test_user)
        ->get(route('get.manual_location', $manual_loaction->location_id))
        ->assertOk()
        ->assertJson(['name' => $expected_name]);
});

test('an unresolved location is flagged as a manual location', function () {
    // A location with no locatable (eager-loaded to null) must still be flagged manual so the
    // assets view offers "add location information"; a resolved station must not be.
    $unknown = Location::factory()->create(['location_id' => 424242]);
    $unknown = Location::with(['locatable' => ['system']])->find($unknown->location_id);
    $station = Location::factory()->for(Station::factory(), 'locatable')->create();
    $station = Location::with(['locatable' => ['system']])->find($station->location_id);

    expect((new LocationRessource($unknown))->resolve()['is_manual_location'])->toBeTrue()
        ->and((new LocationRessource($station))->resolve()['is_manual_location'])->toBeFalse();
});

test('one can submit suggestion', function () {
    Bus::fake();

    $manual_loaction = ManualLocation::factory()->make();

    $response = $this->actingAs($this->test_user)
        ->post(route('post.manual_location'), [
            'name' => $manual_loaction->name,
            'location_id' => $manual_loaction->location_id,
            'solar_system_id' => $manual_loaction->solar_system_id,
        ])->assertRedirect();

    Bus::dispatchedAfterResponse(ResolveUniverseSystemBySystemIdJob::class);
});

test('one get own suggestion', function () {
    $manual_loaction = ManualLocation::factory()->create([
        'user_id' => $this->test_user->id,
    ]);

    $response = $this->actingAs($this->test_user)
        ->get(route('get.manual_location', $manual_loaction->location_id))
        ->assertOk()
        ->assertJson(['name' => $manual_loaction->name]);
});

test('one get suggestion of other user', function () {
    ManualLocation::factory()->count(5)->create([
        'location_id' => 12345,
        'user_id' => User::factory(),
        'created_at' => carbon()->subDay(),
    ]);

    $manual_loaction = ManualLocation::factory()->create([
        'location_id' => 12345,
        'user_id' => User::factory(),
    ]);

    $this->actingAs($this->test_user)
        ->get(route('get.manual_location', 12345))
        ->assertOk()
        ->assertJson(['name' => $manual_loaction->name]);
});

test('admin can accept suggestion', function () {
    ManualLocation::factory()->count(4)->create([
        'location_id' => 12345,
        'user_id' => User::factory(),
        'created_at' => carbon()->subDay(),
    ]);

    $manual_location = ManualLocation::factory()->create([
        'location_id' => 12345,
        'user_id' => User::factory(),
    ]);

    assignPermission($this->test_user, ['manage manual locations']);

    // first visit Manage view
    $response = $this->actingAs($this->test_user)
        ->get(route('manage.manual_locations'))
        ->assertOk();

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/ManualLocations/ManualLocation'));

    // load suggestions via the page's deferred `data` prop (partial Inertia reload)
    expect(loadSuggestions($this)->json('props.data'))->toHaveCount(5);

    // Make sure there is no suggestion in universe_locations
    $this->assertNull(Location::firstWhere(['location_id' => 12345]));

    // accept one
    $response = $this->actingAs($this->test_user)
        ->post(route('accept.manual_locations'), [
            'id' => $manual_location->id,
            'location_id' => $manual_location->location_id,
        ])
        ->assertRedirect(route('manage.manual_locations'));

    // Make sure there is one suggestion in universe_locations
    $this->assertCount(1, Location::where('location_id', 12345)->get());

    // the accepted location's polymorphic relation resolves to the chosen suggestion, so the
    // asset views (LocationRessource::name) render its name rather than a blank " - ".
    $accepted = Location::with('locatable')->firstWhere('location_id', 12345);
    expect($accepted->locatable)->toBeInstanceOf(ManualLocation::class)
        ->and($accepted->locatable->name)->toBe($manual_location->name);

    // check that there there is only one left after accepting
    expect(loadSuggestions($this)->json('props.data'))->toHaveCount(1);
});

test('one get accepted suggestion', function () {
    ManualLocation::factory()->count(4)->create([
        'location_id' => 12345,
        'user_id' => User::factory(),
        'created_at' => carbon()->subDay(),
    ]);

    $manual_location = ManualLocation::factory()->create([
        'user_id' => User::factory(),
        'location_id' => 12345,
    ]);

    assignPermission($this->test_user, ['manage manual locations']);

    // Make sure there is no suggestion in universe_locations
    $this->assertNull(Location::firstWhere(['location_id' => 12345]));

    // accept one
    $response = $this->actingAs($this->test_user)
        ->post(route('accept.manual_locations'), [
            'id' => $manual_location->id,
            'location_id' => $manual_location->location_id,
        ])
        ->assertRedirect(route('manage.manual_locations'));

    // Make sure there is one suggestion in universe_locations
    $this->assertCount(1, Location::where('location_id', 12345)->get());

    // Lookup name
    $this->actingAs($this->test_user)
        ->get(route('get.manual_location', $manual_location->location_id))
        ->assertOk()
        ->assertJson(['name' => $manual_location->name]);
});

test('if location is resolved via jobs delete manual suggestions', function () {
    $manual_location = ManualLocation::factory()->create([
        'user_id' => User::factory(),
    ]);

    $station = Station::factory()->create([
        'station_id' => $manual_location->location_id,
    ]);

    $location = Location::factory()->create([
        'location_id' => $manual_location->location_id,
        'locatable_id' => $manual_location->location_id,
        'locatable_type' => Station::class,
    ]);

    assignPermission($this->test_user, ['manage manual locations']);

    // loading suggestions prunes rows whose location has since been resolved
    expect(loadSuggestions($this)->json('props.data'))->toHaveCount(0)
        ->and(ManualLocation::all())->toBeEmpty();
});

/**
 * Resolve the Manual Locations page's deferred `data` prop via a partial Inertia reload.
 */
function loadSuggestions(TestCase $case)
{
    return $case->actingAs($case->test_user)
        ->get(route('manage.manual_locations'), [
            'X-Inertia' => 'true',
            'X-Inertia-Partial-Component' => 'Configuration/ManualLocations/ManualLocation',
            'X-Inertia-Partial-Data' => 'data',
        ])
        ->assertOk();
}

test('if location does not have system dispatch job', function () {
    $manual_location = ManualLocation::factory()->create();

    // Lookup name
    $this->actingAs($this->test_user)
        ->get(route('get.manual_location', $manual_location->location_id))
        ->assertOk()
        ->assertJson(['name' => $manual_location->name]);

    Queue::assertPushedOn('low', ResolveUniverseSystemBySystemIdJob::class);
});
