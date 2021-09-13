<?php


use Illuminate\Support\Facades\Event;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Web\Http\Resources\Universe\GroupResource;

test('correct data is returned in response', function () {
    $group = Event::fakeFor(fn () => Group::factory()->create());

    $resource = (new GroupResource($group));

    expect($resource instanceof GroupResource)->toBeTrue();
    expect($resource->name)->toEqual($group->name);
});
