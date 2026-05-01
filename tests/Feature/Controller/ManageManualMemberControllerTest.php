<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\Permissions\Role;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

it('denies ManageManualMemberController to unauthenticated user', function () {
    test()->post(route('acl.member.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();
});

it('denies ManageManualMemberController without permission', function () {
    test()->actingAs(test()->test_user)
        ->post(route('acl.member.add', [test()->role->id, test()->test_user->id]))
        ->assertForbidden();
});

it('adds a member to a manual role', function () {
    expect(test()->test_user->hasRole('test'))->toBeFalse();

    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->post(route('acl.member.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->refresh()->roles->isNotEmpty())->toBeTrue();
});

it('removes a member from a manual role', function () {
    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->post(route('acl.member.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->refresh()->roles->isNotEmpty())->toBeTrue();

    test()->actingAs(test()->test_user)
        ->delete(route('acl.member.remove', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->refresh()->roles->isEmpty())->toBeTrue();
});
