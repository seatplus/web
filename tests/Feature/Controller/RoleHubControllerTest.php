<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test', 'type' => 'on-request']);
    $this->role = Role::query()->findOrFail($role->id);
});

it('denies the hub to an unauthenticated user', function () {
    $this->get(route('acl.hub.show', $this->role->id))
        ->assertRedirect();
});

it('forbids the hub to a user with no relationship to the role', function () {
    // A managed group the user is neither a member of, a moderator of, nor eligible to join.
    $this->role->update(['type' => RoleType::MANUAL]);

    $this->actingAs($this->test_user)
        ->get(route('acl.hub.show', $this->role->id))
        ->assertForbidden();
});

it('shows a member only the overview, with no management payloads', function () {
    $this->role->update(['type' => RoleType::MANUAL]);
    RoleMembership::create([
        'role_id' => $this->role->id,
        'entity_type' => User::class,
        'entity_id' => $this->test_user->getKey(),
        'status' => 'active',
    ]);

    $this->actingAs($this->test_user)
        ->get(route('acl.hub.show', $this->role->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/RoleHub')
            ->where('canConfigure', false)
            ->where('canManageMembers', false)
            ->where('initialTab', 'overview')
            ->has('members', 0)
            ->has('applicants', 0)
            ->has('moderators', 0)
            ->has('joinMethods', 0)
            ->has('availablePermissions', 0));
});

it('gives a moderator the members payload but not configuration', function () {
    RoleMembership::create([
        'role_id' => $this->role->id,
        'entity_type' => User::class,
        'entity_id' => $this->test_user->getKey(),
        'can_moderate' => true,
        'status' => 'active',
    ]);

    $this->actingAs($this->test_user)
        ->get(route('acl.hub.show', $this->role->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/RoleHub')
            ->where('canManageMembers', true)
            ->where('canConfigure', false)
            ->where('canManageModerators', false)
            ->has('moderators', 1)
            ->has('joinMethods', 0));
});

it('gives an admin every tab and payload', function () {
    assignPermission($this->test_user, ['administrate access control groups']);

    $this->actingAs($this->test_user)
        ->get(route('acl.hub.show', $this->role->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/RoleHub')
            ->where('canConfigure', true)
            ->where('canManageMembers', true)
            ->where('canManageModerators', true)
            ->has('members')
            ->has('applicants')
            ->has('moderators')
            ->has('joinMethods', 4)
            ->has('availablePermissions'));
});

it('honours a valid requested tab and defaults unknown tabs to overview', function () {
    assignPermission($this->test_user, ['administrate access control groups']);

    $this->actingAs($this->test_user)
        ->get(route('acl.hub.show', ['role_id' => $this->role->id, 'tab' => 'configure']))
        ->assertInertia(fn (Assert $page) => $page->where('initialTab', 'configure'));

    $this->actingAs($this->test_user)
        ->get(route('acl.hub.show', ['role_id' => $this->role->id, 'tab' => 'bogus']))
        ->assertInertia(fn (Assert $page) => $page->where('initialTab', 'overview'));
});
