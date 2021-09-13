<?php


use Seatplus\Web\Tests\TestCase;

uses(TestCase::class);

test('invoke', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('load.character.assets'));

    $response->assertOk();

});
