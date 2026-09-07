<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\RoleMembershipStatus;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    $this->role = Role::query()->findOrFail($role->id);
});

it('denies ApplyToRoleController to unauthenticated user', function () {
    $this->post(route('acl.apply', $this->role->id))
        ->assertRedirect();
});

it('user can apply to on-request role', function () {
    $admin = User::factory()->create();
    assignPermission($admin, ['superuser']);
    $this->actingAs($admin)
        ->postJson(route('acl.update.on-request', $this->role->id), [
            'affiliated' => [
                ['entity_id' => $this->test_character->corporation->corporation_id, 'entity_type' => 'corporation', 'affiliation_type' => 'allowed'],
            ],
            'assigned' => [
                ['entity_id' => $this->test_character->corporation->corporation_id, 'entity_type' => 'corporation'],
            ],
        ])
        ->assertRedirect();

    assignPermission($this->test_user, ['view access control']);

    $this->actingAs($this->test_user)
        ->post(route('acl.apply', $this->role->id))
        ->assertRedirect();

    expect(
        $this->role->roleMemberships()
            ->where('entity_type', User::class)
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->first()
            ?->entity_id
    )->toEqual($this->test_user->id);
});

it('returns 403 when applying to a manual role', function () {
    assignPermission($this->test_user, ['view access control']);

    $this->actingAs($this->test_user)
        ->post(route('acl.apply', $this->role->id))
        ->assertForbidden();
});

it('returns 403 when applying to an automatic role', function () {
    assignPermission($this->test_user, ['administrate access control groups', 'view access control']);

    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.automatic', $this->role->id), [])
        ->assertRedirect();

    $this->actingAs($this->test_user)
        ->post(route('acl.apply', $this->role->id))
        ->assertForbidden();
});

it('returns 403 when applying to an opt-in role', function () {
    assignPermission($this->test_user, ['administrate access control groups', 'view access control']);

    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.opt-in', $this->role->id), [])
        ->assertRedirect();

    $this->actingAs($this->test_user)
        ->post(route('acl.apply', $this->role->id))
        ->assertForbidden();
});
