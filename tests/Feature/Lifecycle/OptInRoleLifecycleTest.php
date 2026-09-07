<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'opt-in role']);
    $this->role = Role::query()->findOrFail($role->id);

    // Dedicated admin so test_user can be the member
    $this->admin = User::factory()->create();
    assignPermission($this->admin, ['administrate access control groups']);

    $this->test_character = $this->test_character->refresh();
});

it('sets opt-in type and affiliations via HTTP', function () {
    expect($this->role->type)->toBe(RoleType::MANUAL);

    $this->actingAs($this->admin)
        ->postJson(route('acl.update.opt-in', $this->role->id), [
            'affiliated' => [
                [
                    'entity_id' => $this->test_character->corporation->corporation_id,
                    'entity_type' => 'corporation',
                    'affiliation_type' => AffiliationType::ALLOWED->value,
                ],
            ],
        ])
        ->assertRedirect(route('acl.hub.show', $this->role->id));

    expect($this->role->fresh()->type)->toBe(RoleType::OPT_IN)
        ->and($this->role->fresh()->affiliations->isNotEmpty())->toBeTrue();
});

it('eligible user can join opt-in role and then leave via HTTP', function () {
    // Set opt-in type and join criteria via HTTP
    $this->actingAs($this->admin)
        ->postJson(route('acl.update.opt-in', $this->role->id), [
            'assigned' => [
                [
                    'entity_id' => $this->test_character->corporation->corporation_id,
                    'entity_type' => 'corporation',
                ],
            ],
        ])
        ->assertRedirect(route('acl.hub.show', $this->role->id));

    expect($this->role->fresh()->type)->toBe(RoleType::OPT_IN);

    // Eligible user joins via HTTP
    assignPermission($this->test_user, ['view access control']);

    $this->actingAs($this->test_user)
        ->post(route('acl.join', $this->role->id))
        ->assertRedirect();

    expect($this->test_user->fresh()->hasRole($this->role))->toBeTrue();

    // Member leaves via HTTP
    $this->actingAs($this->test_user)
        ->delete(route('acl.leave', [$this->role->id, $this->test_user->id]))
        ->assertRedirect();

    expect($this->test_user->fresh()->hasRole($this->role))->toBeFalse();
});

it('opt-in role accepts moderator assignment', function () {
    $this->actingAs($this->admin)
        ->postJson(route('acl.update.opt-in', $this->role->id), [])
        ->assertRedirect();

    $this->actingAs($this->admin)
        ->post(route('acl.moderator.add', [$this->role->id, $this->admin->id]))
        ->assertRedirect();

    expect($this->role->refresh()->roleMemberships()->where('can_moderate', true)->exists())->toBeTrue();
});
