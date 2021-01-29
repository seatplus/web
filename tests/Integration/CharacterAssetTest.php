<?php


namespace Seatplus\Web\Tests\Integration;


use ClaudioDekker\Inertia\Assert;
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Assets\CharacterAsset;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Tests\TestCase;

class CharacterAssetTest extends TestCase
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
        $response = $this->actingAs($this->test_user)
            ->get(route('get.affiliated.characters','assets'));
            //->assertOk();

        $response->assertOk();

    }

}
