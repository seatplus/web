<?php


namespace Seatplus\Web\Tests\Unit\Resources;


use Illuminate\Support\Facades\Event;
use Seatplus\Eveapi\Models\Universe\Type;
use Seatplus\Web\Http\Resources\Universe\TypeResource;
use Seatplus\Web\Tests\TestCase;

class TypeResourceTest extends TestCase
{
    /** @test */
    public function testCorrectDataIsReturnedInResponse()
    {

        $type = Event::fakeFor(fn () => Type::factory()->create());

        $resource = (new TypeResource($type));

        $this->assertTrue($resource instanceof TypeResource);
        $this->assertEquals($type->name, $resource->name);

    }
}