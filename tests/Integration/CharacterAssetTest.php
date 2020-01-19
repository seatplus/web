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

        $response = $this->actingAs($this->test_user)
            ->get(route('character.assets'));

        $response->assertHasProp('assets');
    }

}
