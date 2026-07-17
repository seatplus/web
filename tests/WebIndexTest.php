<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Web\Tests\TestCase;

uses(TestCase::class);

test('redirects to login if unauthorized', function () {
    $response = test()->get('/home');

    $response->assertRedirect('login');
});

test('redirects to login vue component if unauthorized', function () {
    $response = test()->followingRedirects()
        ->get('/home');

    $response->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
});

test('redirects to home if authorized', function () {
    $response = test()->actingAs(test()->test_user)
        ->get('/home');

    // openEnlistments is deferred: absent from the initial render, resolved on partial reload.
    $response->assertInertia(
        fn (Assert $page) => $page
            ->component('Dashboard/Index')
            ->missing('openEnlistments')
            ->loadDeferredProps(fn (Assert $reload) => $reload->has('openEnlistments'))
    );

    test()->assertAuthenticatedAs(test()->test_user);
    expect(auth()->check())->toBeTrue();
});

test('logout if authorized', function () {
    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->post(route('logout'));

    expect(auth()->check())->toBeFalse();
});
