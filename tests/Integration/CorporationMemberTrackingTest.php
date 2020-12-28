<?php


namespace Seatplus\Web\Tests\Integration;


use Seatplus\Web\Tests\TestCase;

class CorporationMemberTrackingTest extends  TestCase
{

    /** @test */
    public function hasDispatchableJob()
    {

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->get(route('corporation.member_tracking'));

        $response->assertInertiaHas('dispatch_transfer_object');
    }

}
