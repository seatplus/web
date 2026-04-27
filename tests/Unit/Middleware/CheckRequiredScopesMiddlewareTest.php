<?php

use Illuminate\Http\Request;
use Mockery\MockInterface;
use Seatplus\Auth\Services\SsoScopes\IsUserCompliantService;
use Seatplus\Web\Http\Middleware\CheckRequiredScopes;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    test()->request = Mockery::mock(Request::class);
    test()->next = function ($request) {
        $request->forward();

        return new Response;
    };
});

it('should skip handle if environment is not production', function () {
    test()->request->shouldReceive('forward')->times(1);

    $middleware = Mockery::mock(CheckRequiredScopes::class, [])
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $middleware->shouldReceive('redirectTo')->times(0);

    $middleware->handle(test()->request, test()->next);
});

it('should call parent method on production environment', function () {
    // set production environment
    app()->detectEnvironment(function () {
        return 'production';
    });

    test()->request->shouldReceive('user')->andReturn(test()->test_user);

    $middleware = Mockery::mock(CheckRequiredScopes::class, [mock(IsUserCompliantService::class, function (MockInterface $mock) {
        $mock->shouldReceive('check')->andReturnFalse();
        $mock->shouldReceive('getMissingScopes')->andReturn(['foo' => 'bar']);
    })])
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $middleware->shouldReceive('redirectTo')->times(1);

    $middleware->handle(test()->request, test()->next);
});

it('redirects to render action', function () {})->todo(
    'Need to figure out how to test this'
);
