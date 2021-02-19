<?php


namespace Seatplus\Web\Tests\Integration;


use ClaudioDekker\Inertia\Assert;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Web\Models\Asset\Asset;
use Seatplus\Web\Tests\TestCase;

class AssetTest extends TestCase
{

    /** @test */
    public function isProtectedByAuthentication()
    {

        $response = $this->followingRedirects()
            ->get(route('character.assets'));

        $response->assertInertia( fn (Assert $page) => $page->component('Auth/Login'));
    }

    /** @test */
    public function see_component()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('character.assets'));

        $response->assertInertia( fn (Assert $page) => $page->component('Character/Assets'));
    }

    /** @test */
    public function load_asset()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('load.character.assets'));

        $response->assertOk();
    }

    /** @test */
    public function it_has_asset_prop()
    {

        $character_assets = Asset::factory()->create([
            'assetable_id' => $this->test_character->character_id,
            'assetable_type' => CharacterInfo::class
        ]);

        // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
        //$this->app->instance('path.public', __DIR__ .'/../../src/public');

        $response = $this->actingAs($this->test_user)
            ->get(route('character.assets'));

        //dd($response->exception->getMessage());

        $response->assertInertia( fn (Assert $page) => $page->has('filters'));
    }

    /** @test */
    public function it_has_list_affiliated_character_list_route()
    {
        Asset::factory()->create([
            'assetable_id' => $this->test_character->character_id,
            'assetable_type' => CharacterInfo::class
        ]);

        $response = $this->actingAs($this->test_user)
            ->get(route('get.affiliated.characters','assets'));
            //->assertOk();

        $response->assertOk();

    }

    /** @test */
    public function load_asset_in_system()
    {
        $asset = Asset::factory()
            ->create([
                'assetable_id' => $this->test_character->character_id,
                'location_id' => Location::factory()->for(Station::factory(), 'locatable'),
                'location_flag' => 'Hangar'
            ]);

        $system = $asset->location->locatable->system;

        // call without filter
        $response = $this->actingAs($this->test_user)
            ->get(route('load.character.assets'))
            ->assertOk();

        $this->assertCount(1, $response->original);

        // call with system_id filter
        $response = $this->actingAs($this->test_user)
            ->get(route('load.character.assets', [
                'systems' => $system->system_id
            ]));

        $this->assertCount(1, $response->original);

        $response = $this->actingAs($this->test_user)
            ->get(route('load.character.assets', [
                'systems' => [$system->system_id]
            ]));

        $this->assertCount(1, $response->original);

        // call with system_id + 1 filter and expect no assets to be found
        $response = $this->actingAs($this->test_user)
            ->get(route('load.character.assets', [
                'systems' => $system->system_id+1
            ]));

        $this->assertCount(0, $response->original);
    }

    /** @test */
    public function load_asset_in_region()
    {
        $asset = Asset::factory()
            ->create([
                'assetable_id' => $this->test_character->character_id,
                'location_id' => Location::factory()->for(Station::factory(), 'locatable'),
                'location_flag' => 'Hangar'
            ]);

        $region = $asset->location->locatable->system->region;


        // call without filter
        $response = $this->actingAs($this->test_user)
            ->get(route('load.character.assets'))
            ->assertOk();

        $this->assertCount(1, $response->original);

        // call with system_id filter
        $response = $this->actingAs($this->test_user)
            ->get(route('load.character.assets', [
                'regions' => $region->region_id
            ]));

        $this->assertCount(1, $response->original);

        $response = $this->actingAs($this->test_user)
            ->get(route('load.character.assets', [
                'regions' => [$region->region_id]
            ]));

        $this->assertCount(1, $response->original);

        // call with system_id + 1 filter and expect no assets to be found
        $response = $this->actingAs($this->test_user)
            ->get(route('load.character.assets', [
                'systems' => $region->region_id+1
            ]));

        $this->assertCount(0, $response->original);
    }

}
