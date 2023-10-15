<?php

use Illuminate\Http\Request;
use Seatplus\Web\Http\Middleware\CheckRequiredScopes;

beforeEach(function () {
    test()->request = Mockery::mock(Request::class);
    test()->next = function ($request) {
        $request->forward();
    };

    test()->middleware = Mockery::mock(CheckRequiredScopes::class, [])
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();
});

it('should skip handle if environment is not production', function () {
    test()->request->shouldReceive('forward')->times(1);
    test()->middleware->shouldReceive('redirectTo')->times(0);

    test()->middleware->handle(test()->request, test()->next);
});

it('should call parent method on production environment', function () {
    // set production environment
    app()->detectEnvironment(function () {
        return 'production';
    });

    test()->actingAs(test()->test_user);
    $user_id = test()->test_user->id;
    $cache_key = "UserScopes:{$user_id}";

    // Cache missing scopes for a user
    Cache::shouldReceive('tags')
        ->with(['characters_with_missing_scopes', $user_id])
        ->andReturnSelf();

    Cache::shouldReceive('get')->with($cache_key)->andReturn(collect(['foo' => 'bar']));

    test()->middleware->shouldReceive('redirectTo')->times(1);

    test()->middleware->handle(test()->request, test()->next);
});
