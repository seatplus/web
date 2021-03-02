<?php


namespace Seatplus\Web\Tests\Integration;


use ClaudioDekker\Inertia\Assert;
use Illuminate\Support\Facades\Queue;
use Seatplus\Eveapi\Actions\Jobs\Universe\ResolveUniverseSystemsBySystemIdAction;
use Seatplus\Eveapi\Jobs\Universe\ResolveUniverseSystemBySystemIdJob;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Eveapi\Models\Universe\Structure;
use Seatplus\Web\Models\ManualLocation;
use Seatplus\Web\Tests\TestCase;

class ManualLocationLifecycleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    /** @test */
    public function it_resolves_unknown_location()
    {
        $manual_loaction = ManualLocation::factory()->make();

        $expected_name = sprintf('Unknown Structure (%s)', $manual_loaction->location_id);

        $this->actingAs($this->test_user)
            ->get(route('get.manual_location', $manual_loaction->location_id))
            ->assertOk()
            ->assertJson(['name' => $expected_name]);

    }

    /** @test */
    public function one_can_submit_suggestion()
    {
        $manual_loaction = ManualLocation::factory()->make();

        $mock = \Mockery::mock('overload:' . ResolveUniverseSystemsBySystemIdAction::class);
        $mock->shouldReceive('execute')->with($manual_loaction->solar_system_id);

        $response = $this->actingAs($this->test_user)
            ->post(route('post.manual_location'), [
                'name' => $manual_loaction->name,
                'location_id' => $manual_loaction->location_id,
                'solar_system_id' => $manual_loaction->solar_system_id
            ])->assertRedirect();
    }

    /** @test */
    public function one_get_own_suggestion()
    {
        $manual_loaction = ManualLocation::factory()->create([
            'user_id' => $this->test_user->id
        ]);

        $this->actingAs($this->test_user)
            ->get(route('get.manual_location', $manual_loaction->location_id))
            ->assertOk()
            ->assertJson(['name' => $manual_loaction->name]);
    }

    /** @test */
    public function one_get_suggestion_of_other_user()
    {
        ManualLocation::factory()->count(5)->create([
            'location_id' => 12345,
            'created_at' => carbon()->subDay()
        ]);

        $manual_loaction = ManualLocation::factory()->create([
            'location_id' => 12345,
        ]);

        $this->actingAs($this->test_user)
            ->get(route('get.manual_location', 12345))
            ->assertOk()
            ->assertJson(['name' => $manual_loaction->name]);
    }

    /** @test */
    public function admin_can_accept_suggestion()
    {
        ManualLocation::factory()->count(4)->create([
            'location_id' => 12345,
            'created_at' => carbon()->subDay()
        ]);

        $manual_location = ManualLocation::factory()->create([
            'location_id' => 12345,
        ]);

        $this->givePermissionsToTestUser(['manage manual locations']);

        // first visit Manage view
        $response = $this->actingAs($this->test_user)
            ->get(route('manage.manual_locations'))
            ->assertOk();

        $response->assertInertia(fn(Assert $page) => $page->component('Configuration/ManualLocations/ManualLocation'));

        // load suggestions
        $response = $this->actingAs($this->test_user)
            ->get(route('get.manuel_locations.suggestions'))
            ->assertOk();

        // check that there are 5 suggestions
        $this->assertCount(5,json_decode($response->content())->data);

        // Make sure there is no suggestion in universe_locations
        $this->assertNull(Location::firstWhere(['location_id' =>12345]));

        // accept one
        $response = $this->actingAs($this->test_user)
            ->post(route('get.manuel_locations.suggestions'), [
                'id' => $manual_location->id,
                'location_id' => $manual_location->location_id
            ])
            ->assertRedirect(route('manage.manual_locations'));

        // Make sure there is one suggestion in universe_locations
        $this->assertCount(1, Location::where('location_id',12345)->get());

        // check that there there is only one left after accepting
        $response = $this->actingAs($this->test_user)
            ->get(route('get.manuel_locations.suggestions'))
            ->assertOk();
        $this->assertCount(1,json_decode($response->content())->data);

    }

    /** @test */
    public function one_get_accepted_suggestion()
    {
        ManualLocation::factory()->count(4)->create([
            'location_id' => 12345,
            'created_at' => carbon()->subDay()
        ]);

        $manual_location = ManualLocation::factory()->create([
            'location_id' => 12345,
        ]);

        $this->givePermissionsToTestUser(['manage manual locations']);

        // Make sure there is no suggestion in universe_locations
        $this->assertNull(Location::firstWhere(['location_id' =>12345]));

        // accept one
        $response = $this->actingAs($this->test_user)
            ->post(route('get.manuel_locations.suggestions'), [
                'id' => $manual_location->id,
                'location_id' => $manual_location->location_id
            ])
            ->assertRedirect(route('manage.manual_locations'));

        // Make sure there is one suggestion in universe_locations
        $this->assertCount(1, Location::where('location_id',12345)->get());

        // Lookup name
        $this->actingAs($this->test_user)
            ->get(route('get.manual_location', $manual_location->location_id))
            ->assertOk()
            ->assertJson(['name' => $manual_location->name]);

    }

    /** @test */
    public function if_location_is_resolved_via_jobs_delete_manual_suggestions()
    {

        $manual_location = ManualLocation::factory()->create();

        $station = Station::factory()->create([
            'station_id' => $manual_location->location_id
        ]);

        $location = Location::factory()->create([
            'location_id' => $manual_location->location_id,
            'locatable_id' => $manual_location->location_id,
            'locatable_type' => Station::class
        ]);

        $this->givePermissionsToTestUser(['manage manual locations']);

        // load suggestions
        $response = $this->actingAs($this->test_user)
            ->get(route('get.manuel_locations.suggestions'))
            ->assertOk();

        // check that there are no suggestions
        $this->assertCount(0,json_decode($response->content())->data);
        $this->assertEmpty(ManualLocation::all());

    }

    /** @test */
    public function if_location_does_not_have_system_dispatch_job()
    {
        $manual_location = ManualLocation::factory()->create();

        // Lookup name
        $this->actingAs($this->test_user)
            ->get(route('get.manual_location', $manual_location->location_id))
            ->assertOk()
            ->assertJson(['name' => $manual_location->name]);

        Queue::assertPushedOn('low', ResolveUniverseSystemBySystemIdJob::class);
    }

}
