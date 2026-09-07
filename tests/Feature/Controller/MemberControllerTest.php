<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    $this->role = Role::query()->findOrFail($role->id);
});

it('denies member endpoints to unauthenticated user', function () {
    $this->post(route('acl.member.add', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();
});

it('denies member endpoints without permission', function () {
    $this->actingAs($this->test_user)
        ->post(route('acl.member.add', [$this->role->id, $this->test_user->id]))
        ->assertForbidden();
});

it('adds a member to a manual role', function () {
    expect($this->test_user->hasRole('test'))->toBeFalse();

    assignPermission($this->test_user, ['administrate access control groups']);

    $this->actingAs($this->test_user)
        ->post(route('acl.member.add', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->test_user->refresh()->roles->isNotEmpty())->toBeTrue();
});

it('removes a member from a manual role', function () {
    assignPermission($this->test_user, ['administrate access control groups']);

    $this->actingAs($this->test_user)
        ->post(route('acl.member.add', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->test_user->refresh()->roles->isNotEmpty())->toBeTrue();

    $this->actingAs($this->test_user)
        ->delete(route('acl.member.remove', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->test_user->refresh()->roles->isEmpty())->toBeTrue();
});

it('admin can remove a member from an on-request role', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['superuser']);

    // Set up on-request role and get user to apply + be approved
    $this->actingAs($admin)
        ->postJson(route('acl.update.on-request', $this->role->id), [
            'assigned' => [
                ['entity_id' => $this->test_character->corporation->corporation_id, 'entity_type' => 'corporation'],
            ],
        ])
        ->assertRedirect();

    $this->actingAs($this->test_user)
        ->post(route('acl.apply', $this->role->id))
        ->assertRedirect();

    $this->actingAs($admin)
        ->post(route('acl.approve', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->test_user->fresh()->hasRole($this->role))->toBeTrue();

    $this->actingAs($admin)
        ->delete(route('acl.member.remove', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->test_user->fresh()->hasRole($this->role))->toBeFalse();
});

it('admin can remove a member from an opt-in role', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['superuser']);

    // Set up opt-in role with join criteria matching the test user's corporation
    $this->actingAs($admin)
        ->postJson(route('acl.update.opt-in', $this->role->id), [
            'assigned' => [
                ['entity_id' => $this->test_character->corporation->corporation_id, 'entity_type' => 'corporation'],
            ],
        ])
        ->assertRedirect();

    $this->actingAs($this->test_user)
        ->post(route('acl.join', $this->role->id))
        ->assertRedirect();

    expect($this->test_user->fresh()->hasRole($this->role))->toBeTrue();

    $this->actingAs($admin)
        ->delete(route('acl.member.remove', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->test_user->fresh()->hasRole($this->role))->toBeFalse();
});

it('returns 422 when trying to remove a member from an automatic role', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['superuser']);

    $this->actingAs($admin)
        ->postJson(route('acl.update.automatic', $this->role->id), [])
        ->assertRedirect();

    $this->actingAs($admin)
        ->delete(route('acl.member.remove', [$this->role->id, $this->test_user->id]))
        ->assertStatus(422);
});

it('allows a moderator to add a member to a manual role', function () {
    $moderator = User::factory()->create();

    assignPermission($this->test_user, ['administrate access control groups']);

    $this->actingAs($this->test_user)
        ->post(route('acl.moderator.add', [$this->role->id, $moderator->id]))
        ->assertRedirect();

    $member = User::factory()->create();

    $this->actingAs($moderator)
        ->post(route('acl.member.add', [$this->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole($this->role))->toBeTrue();
});

it('allows a moderator to remove a member from a manual role', function () {
    $moderator = User::factory()->create();

    assignPermission($this->test_user, ['administrate access control groups']);

    $this->actingAs($this->test_user)
        ->post(route('acl.moderator.add', [$this->role->id, $moderator->id]))
        ->assertRedirect();

    $member = User::factory()->create();

    $this->actingAs($this->test_user)
        ->post(route('acl.member.add', [$this->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole($this->role))->toBeTrue();

    $this->actingAs($moderator)
        ->delete(route('acl.member.remove', [$this->role->id, $member->id]))
        ->assertRedirect();

    expect($member->fresh()->hasRole($this->role))->toBeFalse();
});

it('denies a non-moderator non-admin from adding a member', function () {
    $other = User::factory()->create();

    $this->actingAs($other)
        ->post(route('acl.member.add', [$this->role->id, $this->test_user->id]))
        ->assertForbidden();
});

it('denies a non-moderator non-admin from removing a member', function () {
    $other = User::factory()->create();

    $this->actingAs($other)
        ->delete(route('acl.member.remove', [$this->role->id, $this->test_user->id]))
        ->assertForbidden();
});
