<?php


use Inertia\Testing\Assert;

uses(TestCase::class);

test('redirects to login if unauthorized', function () {
    $response = test()->get('/home');

    $response->assertRedirect('auth/login');
});

test('redirects to login vue component if unauthorized', function () {
    // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
    //test()->app->instance('path.public', __DIR__ .'/../src/public');

    $response = test()->followingRedirects()
        ->get('/home');

    $response->assertInertia( fn (Assert $page) => $page->component('Auth/Login'));
});

test('redirects to home if authorized', function () {
    // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
    test()->app->instance('path.public', __DIR__ .'/../src/public');

    $response = test()->actingAs(test()->test_user)
        ->get('/home');


    $response->assertInertia( fn (Assert $page) => $page->component('Dashboard/Index'));

    test()->assertAuthenticatedAs(test()->test_user);
    test()->assertTrue(auth()->check());
});

test('logout if authorized', function () {
    // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
    test()->app->instance('path.public', __DIR__ .'/../src/public');

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->get(route('auth.logout'));

    //$response->assertRedirect('auth/login');
    //$response->assertViewIs('web::auth.login');

    test()->assertFalse(auth()->check());

});
