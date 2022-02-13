<?php


use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    app()->make(PermissionRegistrar::class)->registerPermissions();
});

it('has users list', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('server.settings'));

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/UserList'));
});

it('has server scopes', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('settings.scopes'));

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/Scopes/OverviewScopeSettings'));
});

test('one can impersionate', function () {
    $user_two = Event::fakeFor(fn () => User::factory()->create()) ;

    $response = test()->actingAs(test()->test_user)
        ->get(route('impersonate.start', $user_two->id));

    test()->assertAuthenticatedAs($user_two);
});

test('one can stop impersionate', function () {
    $user_two = Event::fakeFor(fn () => User::factory()->create()) ;

    test()->actingAs(test()->test_user)
        ->get(route('impersonate.start', $user_two->id));

    test()->assertAuthenticatedAs($user_two);

    test()->actingAs($user_two)
        ->get(route('impersonate.stop'));

    test()->assertAuthenticatedAs(test()->test_user);
});
