<?php


use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Web\Tests\TestCase;

uses(TestCase::class);

test('redirects to login if unauthorized', function () {
    $response = test()->get('/home');

    $response->assertRedirect('auth/login');
});

test('redirects to login vue component if unauthorized', function () {
    $response = test()->followingRedirects()
        ->get('/home');

    $response->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
});

test('redirects to home if authorized', function () {
    $response = test()->actingAs(test()->test_user)
        ->get('/home');


    $response->assertInertia(fn (Assert $page) => $page->component('Dashboard/Index'));

    test()->assertAuthenticatedAs(test()->test_user);
    expect(auth()->check())->toBeTrue();
});

test('logout if authorized', function () {
    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->get(route('auth.logout'));

    //$response->assertRedirect('auth/login');
    //$response->assertViewIs('web::auth.login');

    expect(auth()->check())->toBeFalse();
});
