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

it('denies member endpoints to unauthenticated user', function () {
    test()->post(route('acl.member.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();
});

it('denies member endpoints without permission', function () {
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

it('admin can remove a member from an on-request role', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['superuser']);

    // Set up on-request role and get user to apply + be approved
    test()->actingAs($admin)
        ->postJson(route('acl.update.on-request', test()->role->id), [
            'assigned' => [
                ['entity_id' => test()->test_character->corporation->corporation_id, 'entity_type' => 'corporation'],
            ],
        ])
        ->assertRedirect();

    test()->actingAs(test()->test_user)
        ->post(route('acl.apply', test()->role->id))
        ->assertRedirect();

    test()->actingAs($admin)
        ->post(route('acl.approve', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->fresh()->hasRole(test()->role))->toBeTrue();

    test()->actingAs($admin)
        ->delete(route('acl.member.remove', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->fresh()->hasRole(test()->role))->toBeFalse();
});

it('admin can remove a member from an opt-in role', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['superuser']);

    // Set up opt-in role with join criteria matching the test user's corporation
    test()->actingAs($admin)
        ->postJson(route('acl.update.opt-in', test()->role->id), [
            'assigned' => [
                ['entity_id' => test()->test_character->corporation->corporation_id, 'entity_type' => 'corporation'],
            ],
        ])
        ->assertRedirect();

    test()->actingAs(test()->test_user)
        ->post(route('acl.join', test()->role->id))
        ->assertRedirect();

    expect(test()->test_user->fresh()->hasRole(test()->role))->toBeTrue();

    test()->actingAs($admin)
        ->delete(route('acl.member.remove', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->fresh()->hasRole(test()->role))->toBeFalse();
});

it('returns 422 when trying to remove a member from an automatic role', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['superuser']);

    test()->actingAs($admin)
        ->postJson(route('acl.update.automatic', test()->role->id), [])
        ->assertRedirect();

    test()->actingAs($admin)
        ->delete(route('acl.member.remove', [test()->role->id, test()->test_user->id]))
        ->assertStatus(422);
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

    test()->actingAs(test()->test_user)
        ->post(route('acl.member.add', [test()->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole(test()->role))->toBeTrue();

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

it('denies a non-moderator non-admin from removing a member', function () {
    $other = User::factory()->create();

    test()->actingAs($other)
        ->delete(route('acl.member.remove', [test()->role->id, test()->test_user->id]))
        ->assertForbidden();
});
