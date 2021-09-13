<?php


use Illuminate\Support\Facades\Event;
use Seatplus\Web\Models\ManualLocation;
use Seatplus\Web\Tests\TestCase;

uses(TestCase::class);

test('manual location has location relationship', function () {

    $manual_location = Event::fakeFor( fn() => ManualLocation::factory()->create());

    test()->assertNotNull($manual_location);
});
