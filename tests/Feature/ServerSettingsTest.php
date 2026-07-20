<?php

use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
});

it('has users list', function () {
    // A second user whose character/refreshToken is not pre-loaded — UserRessource reads each
    // character's refreshToken for its scopes, so the list query must eager-load it (strict mode
    // would otherwise raise a LazyLoadingViolationException, as it did on /configuration/settings).
    Event::fakeFor(fn () => User::factory()->create());

    $response = test()->actingAs(test()->test_user)
        ->get(route('server.settings'));

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/UserList'));
});

it('shares the settings navigation as a prop', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('server.settings'));

    $response->assertInertia(fn (Assert $page) => $page
        ->has('settingsNavigation', count(config('web.settings')))
        ->where('settingsNavigation.0.route', 'server.settings')
    );
});

it('has server scopes', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('settings.scopes'));

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/Scopes/OverviewScopeSettings'));
});

test('one can impersionate', function () {
    $user_two = Event::fakeFor(fn () => User::factory()->create());

    $response = test()->actingAs(test()->test_user)
        ->get(route('impersonate.start', $user_two->id));

    test()->assertAuthenticatedAs($user_two);
});

test('one can stop impersionate', function () {
    $user_two = Event::fakeFor(fn () => User::factory()->create());

    test()->actingAs(test()->test_user)
        ->get(route('impersonate.start', $user_two->id));

    test()->assertAuthenticatedAs($user_two);

    test()->actingAs($user_two)
        ->get(route('impersonate.stop'));

    test()->assertAuthenticatedAs(test()->test_user);
});
