<?php


namespace Seatplus\Web\Tests\Unit\Resources;


use Illuminate\Support\Facades\Event;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Web\Http\Resources\Universe\GroupResource;
use Seatplus\Web\Tests\TestCase;

class GroupResourceTest extends TestCase
{
    /** @test */
    public function testCorrectDataIsReturnedInResponse()
    {
        $group = Event::fakeFor(fn () => Group::factory()->create());

        $resource = (new GroupResource($group));

        $this->assertTrue($resource instanceof GroupResource);
        $this->assertEquals($group->name, $resource->name);
    }
}