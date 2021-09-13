<?php


use Illuminate\Support\Facades\Event;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Web\Http\Resources\Universe\GroupResource;
use Seatplus\Web\Tests\TestCase;

uses(TestCase::class);

test('correct data is returned in response', function () {
    $group = Event::fakeFor(fn () => Group::factory()->create());

    $resource = (new GroupResource($group));

    test()->assertTrue($resource instanceof GroupResource);
    test()->assertEquals($group->name, $resource->name);
});
