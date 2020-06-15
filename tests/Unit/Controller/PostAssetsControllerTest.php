<?php


namespace Seatplus\Web\Tests\Unit\Controller;


use Seatplus\Web\Tests\TestCase;

class PostAssetsControllerTest extends TestCase
{

    /** @test */
    public function invoke()
    {
        $this->actingAs($this->test_user)
            ->post(route('load.character.assets'))
            ->assertOk();
    }

}
