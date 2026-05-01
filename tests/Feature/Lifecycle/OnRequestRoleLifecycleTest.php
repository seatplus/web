<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Enums\RoleMembershipStatus;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\DTO\AffiliationData;
use Seatplus\Auth\Services\Roles\DTO\CriteriaData;
use Seatplus\Auth\Services\Roles\OnRequestRoleService;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'on-request role']);
    test()->role = Role::find($role->id);

    // Dedicated admin so test_user can be the applicant
    test()->admin = User::factory()->create();
    assignPermission(test()->admin, ['administrate access control groups']);

    // Ensure corporation relation is available
    test()->test_character = test()->test_character->refresh();
});

it('full lifecycle: configure apply set moderator approve leave', function () {
    // 1. Set on-request type and criteria via service
    $service = new OnRequestRoleService(test()->role);
    $service->setRoleType(RoleType::ON_REQUEST);
    $service->syncAffiliateManyEntities(
        new AffiliationData(test()->test_character->corporation->corporation_id, 'corporation', AffiliationType::ALLOWED)
    );
    $service->addCriteriaForRoleApplication(
        new CriteriaData(test()->test_character->corporation->corporation_id, 'corporation')
    );

    expect(test()->role->fresh()->type)->toBe(RoleType::ON_REQUEST);

    // 2. Applicant applies via HTTP
    assignPermissionToTestUser(['view access control']);
    test()->actingAs(test()->test_user)
        ->post(route('acl.apply', test()->role->id))
        ->assertRedirect();

    expect(
        test()->role->role_memberships()
            ->where('entity_id', test()->test_user->id)
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->exists()
    )->toBeTrue();

    // 3. Set moderator via HTTP
    $moderator = User::factory()->create();
    test()->actingAs(test()->admin)
        ->post(route('acl.moderator.add', [test()->role->id, $moderator->id]))
        ->assertRedirect();

    expect(
        test()->role->role_memberships()
            ->where('entity_id', $moderator->id)
            ->where('can_moderate', true)
            ->exists()
    )->toBeTrue();

    // 4. Moderator approves applicant via HTTP
    test()->actingAs($moderator)
        ->post(route('acl.approve', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->fresh()->hasRole(test()->role))->toBeTrue();

    // 5. Member leaves via HTTP
    test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->fresh()->hasRole(test()->role))->toBeFalse();
});

it('deny flow: configure apply set moderator deny', function () {
    $service = new OnRequestRoleService(test()->role);
    $service->setRoleType(RoleType::ON_REQUEST);
    $service->syncAffiliateManyEntities(
        new AffiliationData(test()->test_character->corporation->corporation_id, 'corporation', AffiliationType::ALLOWED)
    );
    $service->addCriteriaForRoleApplication(
        new CriteriaData(test()->test_character->corporation->corporation_id, 'corporation')
    );

    assignPermissionToTestUser(['view access control']);
    test()->actingAs(test()->test_user)
        ->post(route('acl.apply', test()->role->id))
        ->assertRedirect();

    $moderator = User::factory()->create();
    test()->actingAs(test()->admin)
        ->post(route('acl.moderator.add', [test()->role->id, $moderator->id]))
        ->assertRedirect();

    test()->actingAs($moderator)
        ->delete(route('acl.deny', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(
        test()->role->role_memberships()
            ->where('entity_id', test()->test_user->id)
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->exists()
    )->toBeFalse();

    expect(test()->test_user->fresh()->hasRole(test()->role))->toBeFalse();
});
