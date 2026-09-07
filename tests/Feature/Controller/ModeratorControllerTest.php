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

it('denies moderator endpoints to unauthenticated user', function () {
    $this->post(route('acl.moderator.add', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();
});

it('denies moderator endpoints without permission', function () {
    $this->actingAs($this->test_user)
        ->post(route('acl.moderator.add', [$this->role->id, $this->test_user->id]))
        ->assertForbidden();
});

it('adds a moderator to a manual role', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);

    expect($this->role->roleMemberships()->where('can_moderate', true)->doesntExist())->toBeTrue();

    $this->actingAs($admin)
        ->post(route('acl.moderator.add', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->role->refresh()->roleMemberships()->where('can_moderate', true)->exists())->toBeTrue();
});

it('removes a moderator from a manual role', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);

    $this->actingAs($admin)
        ->post(route('acl.moderator.add', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->role->refresh()->roleMemberships()->where('can_moderate', true)->exists())->toBeTrue();

    $this->actingAs($admin)
        ->delete(route('acl.moderator.remove', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->role->refresh()->roleMemberships()->where('can_moderate', true)->doesntExist())->toBeTrue();
});

it('rejects moderator assignment on automatic roles', function () {
    assignPermission($this->test_user, 'administrate access control groups');

    // Set to automatic via HTTP
    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.automatic', $this->role->id), [])
        ->assertRedirect();

    $this->actingAs($this->test_user)
        ->post(route('acl.moderator.add', [$this->role->id, $this->test_user->id]))
        ->assertForbidden();
});
