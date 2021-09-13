<?php


use Illuminate\Support\Facades\Event;
use Seatplus\Eveapi\Models\Universe\Type;
use Seatplus\Web\Http\Resources\Universe\TypeResource;
use Seatplus\Web\Tests\TestCase;


test('correct data is returned in response', function () {

    $type = Event::fakeFor(fn () => Type::factory()->create());

    $resource = (new TypeResource($type));

    expect($resource instanceof TypeResource)->toBeTrue();
    expect($resource->name)->toEqual($type->name);

});
