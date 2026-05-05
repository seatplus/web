<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

it('denies moderator endpoints to unauthenticated user', function () {
    test()->post(route('acl.moderator.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();
});

it('denies moderator endpoints without permission', function () {
    test()->actingAs(test()->test_user)
        ->post(route('acl.moderator.add', [test()->role->id, test()->test_user->id]))
        ->assertForbidden();
});

it('adds a moderator to a manual role', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);

    expect(test()->role->role_memberships()->where('can_moderate', true)->doesntExist())->toBeTrue();

    test()->actingAs($admin)
        ->post(route('acl.moderator.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->role->refresh()->role_memberships()->where('can_moderate', true)->exists())->toBeTrue();
});

it('removes a moderator from a manual role', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);

    test()->actingAs($admin)
        ->post(route('acl.moderator.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->role->refresh()->role_memberships()->where('can_moderate', true)->exists())->toBeTrue();

    test()->actingAs($admin)
        ->delete(route('acl.moderator.remove', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->role->refresh()->role_memberships()->where('can_moderate', true)->doesntExist())->toBeTrue();
});

it('rejects moderator assignment on automatic roles', function () {
    assignPermissionToTestUser('administrate access control groups');

    // Set to automatic via HTTP
    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), [])
        ->assertRedirect();

    test()->actingAs(test()->test_user)
        ->post(route('acl.moderator.add', [test()->role->id, test()->test_user->id]))
        ->assertForbidden();
});
