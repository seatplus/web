<?php


use Inertia\Inertia;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Inertia::share([
        'flash' => fn () => [
            'success' => session()->pull('success'),
            'info' => session()->pull('info'),
            'warning' => session()->pull('warning'),
            'error' => session()->pull('error'),
        ],
    ]);
});

test('see success flash messages', function () {
    $response = test()->withSession(['success','SuccessFlashMessage'])
        ->get('auth/login');

    $response->assertInertia(fn (Assert $page) => $page->has('flash.success'));
});

test('see error flash messages', function () {
    $response = test()->withSession(['error','ErrorFlashMessage'])
        ->get('auth/login');

    $response->assertInertia(fn (Assert $page) => $page->has('flash.error'));
});

test('see warning flash messages', function () {
    $response = test()->withSession(['warning','WarningFlashMessage'])
        ->get('auth/login');

    $response->assertInertia(fn (Assert $page) => $page->has('flash.warning'));
});

test('see info flash messages', function () {
    $response = test()->withSession(['info','InfoFlashMessage'])
        ->get('auth/login');

    $response->assertInertia(fn (Assert $page) => $page->has('flash.info'));
});
