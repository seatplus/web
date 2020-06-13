<?php


namespace Seatplus\Web\Tests\Integration;


use Seatplus\Eveapi\Models\Assets\CharacterAsset;
use Seatplus\Web\Tests\TestCase;

class CharacterAssetTest extends TestCase
{

    /** @test */
    public function isProtectedByAuthentication()
    {

        $response = $this->followingRedirects()
            ->get(route('character.assets'));

        $response->assertComponent('Auth/Login');
    }

    /** @test */
    public function see_component()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('character.assets'));

        $response->assertComponent('Character/Assets');
    }

    /** @test */
    public function it_has_asset_prop()
    {

        $character_assets = factory(CharacterAsset::class)->create([
            'character_id' => $this->test_character->character_id
        ]);

        // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
        //$this->app->instance('path.public', __DIR__ .'/../../src/public');

        $response = $this->actingAs($this->test_user)
            ->get(route('character.assets'));

        //dd($response->exception->getMessage());

        $response->assertHasProp('filters');
    }

}
