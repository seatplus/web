<?php

use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Web\Tests\TestCase;

uses(TestCase::class);

test('redirects to login if unauthorized', function () {
    $response = $this->get('/home');

    $response->assertRedirect('login');
});

test('redirects to login vue component if unauthorized', function () {
    $response = $this->followingRedirects()
        ->get('/home');

    $response->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
});

test('redirects to home if authorized', function () {
    $response = $this->actingAs($this->test_user)
        ->get('/home');

    $response->assertInertia(fn (Assert $page) => $page->component('Dashboard/Index'));

    $this->assertAuthenticatedAs($this->test_user);
    expect(auth()->check())->toBeTrue();
});

test('logout if authorized', function () {
    $response = $this->actingAs($this->test_user)
        ->followingRedirects()
        ->post(route('logout'));

    expect(auth()->check())->toBeFalse();
});
