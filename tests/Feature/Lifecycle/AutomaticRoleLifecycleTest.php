<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    Queue::fake();
    assignPermission($this->test_user, ['administrate access control groups']);

    $role = Role::create(['name' => 'automatic role']);
    $this->role = Role::query()->findOrFail($role->id);
    $this->test_character = $this->test_character->refresh();
});

it('sets automatic type and affiliations via HTTP', function () {
    expect($this->role->type)->toBe(RoleType::MANUAL);

    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.automatic', $this->role->id), [
            'affiliated' => [
                [
                    'entity_id' => $this->test_character->corporation->corporation_id,
                    'entity_type' => 'corporation',
                    'affiliation_type' => AffiliationType::ALLOWED->value,
                ],
            ],
        ])
        ->assertRedirect(route('acl.hub.show', $this->role->id));

    expect($this->role->fresh()->type)->toBe(RoleType::AUTOMATIC)
        ->and($this->role->fresh()->affiliations->isNotEmpty())->toBeTrue();
});

it('sets assigned criteria for automatic role via HTTP', function () {
    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.automatic', $this->role->id), [
            'assigned' => [
                [
                    'entity_id' => $this->test_character->corporation->corporation_id,
                    'entity_type' => 'corporation',
                ],
            ],
        ])
        ->assertRedirect(route('acl.hub.show', $this->role->id));

    // Verify a role_membership criterion was created for the corporation
    expect(
        $this->role->fresh()->roleMemberships()
            ->where('entity_id', $this->test_character->corporation->corporation_id)
            ->exists()
    )->toBeTrue();
});

it('open-to-all automatic role assigns every user, including unaffiliated ones', function () {
    // a second user in an unrelated corporation — would not match any normal criterion
    $other_user = User::factory()->create();

    // Doomheim (1000001) as the criterion = open to all
    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.automatic', $this->role->id), [
            'assigned' => [
                ['entity_id' => 1000001, 'entity_type' => 'corporation'],
            ],
        ])
        ->assertRedirect(route('acl.hub.show', $this->role->id));

    expect($this->test_user->fresh()->hasRole($this->role->name))->toBeTrue()
        ->and($other_user->fresh()->hasRole($this->role->name))->toBeTrue();
});

it('automatic role rejects moderator assignment', function () {
    $this->actingAs($this->test_user)
        ->postJson(route('acl.update.automatic', $this->role->id), [])
        ->assertRedirect();

    $this->actingAs($this->test_user)
        ->post(route('acl.moderator.add', [$this->role->id, $this->test_user->id]))
        ->assertForbidden();
});
