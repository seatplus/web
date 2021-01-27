<?php


namespace Seatplus\Web\Tests\Integration;


use ClaudioDekker\Inertia\Assert;
use Seatplus\Web\Tests\TestCase;

class CorporationMemberTrackingTest extends  TestCase
{

    /** @test */
    public function hasDispatchableJob()
    {

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->get(route('corporation.member_tracking'));

        $response->assertInertia( fn (Assert $page) => $page->has('dispatch_transfer_object'));
    }

}
