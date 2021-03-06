<?php


namespace Seatplus\Web\Tests\Unit\Controller;


use Seatplus\Web\Tests\TestCase;

class PostAssetsControllerTest extends TestCase
{

    /** @test */
    public function invoke()
    {
        $response = $this->actingAs($this->test_user)
            ->get(route('load.character.assets'));

        $response->assertOk();

    }

}
