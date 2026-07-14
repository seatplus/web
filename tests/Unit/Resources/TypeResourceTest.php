<?php

use Illuminate\Support\Facades\Event;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Type;
use Seatplus\Web\Http\Resources\Universe\TypeResource;

test('correct data is returned in response', function () {
    $type = Event::fakeFor(fn () => Type::factory()->create());

    $resource = (new TypeResource($type));

    expect($resource instanceof TypeResource)->toBeTrue();
    expect($resource->name)->toEqual($type->name);
});

it('derives image_variant from the type inventory category', function (int $categoryId, string $expected) {
    $group = Event::fakeFor(fn () => Group::factory()->create(['category_id' => $categoryId]));
    $type = Event::fakeFor(fn () => Type::factory()->create(['group_id' => $group->group_id]));
    $type->load('group');

    expect((new TypeResource($type))->resolve()['image_variant'])->toBe($expected);
})->with([
    'ship' => [6, 'render'],
    'blueprint' => [9, 'bp'],
    'other' => [17, 'icon'],
]);
