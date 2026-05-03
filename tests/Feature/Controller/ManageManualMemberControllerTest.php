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

it('allows a moderator to add a member to a manual role', function () {
    $moderator = User::factory()->create();

    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->post(route('acl.moderator.add', [test()->role->id, $moderator->id]))
        ->assertRedirect();

    $member = User::factory()->create();

    test()->actingAs($moderator)
        ->post(route('acl.member.add', [test()->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole(test()->role))->toBeTrue();
});

it('allows a moderator to remove a member from a manual role', function () {
    $moderator = User::factory()->create();

    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->post(route('acl.moderator.add', [test()->role->id, $moderator->id]))
        ->assertRedirect();

    $member = User::factory()->create();

    // Admin adds the member first
    test()->actingAs(test()->test_user)
        ->post(route('acl.member.add', [test()->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole(test()->role))->toBeTrue();

    // Moderator removes them
    test()->actingAs($moderator)
        ->delete(route('acl.member.remove', [test()->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole(test()->role))->toBeFalse();
});

it('denies a non-moderator non-admin from adding a member', function () {
    $other = User::factory()->create();

    test()->actingAs($other)
        ->post(route('acl.member.add', [test()->role->id, test()->test_user->id]))
        ->assertForbidden();
});
