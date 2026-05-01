<?php

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

    $role = Role::create(['name' => 'test']);
    test()->role = Role::find($role->id);

    test()->test_character = test()->test_character->refresh();
});

test('user can join waitlist', function () {
    expect(test()->role->affiliations->isEmpty())->toBeTrue();

    assignPermissionToTestUser(['view access control']);

    expect(test()->role->type)->toEqual(RoleType::MANUAL);

    $service = new OnRequestRoleService(test()->role);
    $service->setRoleType(RoleType::ON_REQUEST);
    $service->syncAffiliateManyEntities(
        new AffiliationData(test()->test_character->corporation->corporation_id, 'corporation', AffiliationType::ALLOWED)
    );
    $service->addCriteriaForRoleApplication(
        new CriteriaData(test()->test_character->corporation->corporation_id, 'corporation')
    );

    expect(test()->role->refresh()->affiliations->isEmpty())->toBeFalse();

    expect(test()->test_user->hasRole(test()->role))->toBeFalse();

    $response = test()->actingAs(test()->test_user)
        ->post(route('acl.apply', test()->role->id));

    $response->assertRedirect();

    expect(test()->test_user->hasRole(test()->role))->toBeFalse();

    expect(
        test()->role->role_memberships()
            ->where('entity_type', User::class)
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->first()
            ->entity_id
    )->toEqual(test()->test_user->id);
});

test('moderator can approve applicant', function () {
    $service = new OnRequestRoleService(test()->role);
    $service->setRoleType(RoleType::ON_REQUEST);
    $service->syncAffiliateManyEntities(
        new AffiliationData(test()->test_character->corporation->corporation_id, 'corporation', AffiliationType::ALLOWED)
    );
    $service->addCriteriaForRoleApplication(
        new CriteriaData(test()->test_character->corporation->corporation_id, 'corporation')
    );

    // User applies
    assignPermissionToTestUser(['view access control']);
    test()->actingAs(test()->test_user)
        ->post(route('acl.apply', test()->role->id))
        ->assertRedirect();

    expect(test()->test_user->hasRole(test()->role))->toBeFalse();

    // A moderator approves via HTTP
    $moderator = User::factory()->create();
    $service->setModerator($moderator);

    test()->actingAs($moderator)
        ->post(route('acl.approve', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeTrue();

    expect(
        test()->role->role_memberships()
            ->where('entity_type', User::class)
            ->where('status', RoleMembershipStatus::ACTIVE->value)
            ->first()
            ->entity_id
    )->toEqual(test()->test_user->id);
});

test('moderator can deny applicant', function () {
    $service = new OnRequestRoleService(test()->role);
    $service->setRoleType(RoleType::ON_REQUEST);
    $service->syncAffiliateManyEntities(
        new AffiliationData(test()->test_character->corporation->corporation_id, 'corporation', AffiliationType::ALLOWED)
    );
    $service->addCriteriaForRoleApplication(
        new CriteriaData(test()->test_character->corporation->corporation_id, 'corporation')
    );

    // User applies
    assignPermissionToTestUser(['view access control']);
    test()->actingAs(test()->test_user)
        ->post(route('acl.apply', test()->role->id))
        ->assertRedirect();

    expect(
        test()->role->role_memberships()
            ->where('entity_type', User::class)
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->exists()
    )->toBeTrue();

    // A moderator denies via HTTP
    $moderator = User::factory()->create();
    $service->setModerator($moderator);

    test()->actingAs($moderator)
        ->delete(route('acl.deny', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(
        test()->role->role_memberships()
            ->where('entity_type', User::class)
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->exists()
    )->toBeFalse();
});

test('non-moderator cannot approve applicant', function () {
    $service = new OnRequestRoleService(test()->role);
    $service->setRoleType(RoleType::ON_REQUEST);

    $other_user = User::factory()->create();

    test()->actingAs($other_user)
        ->post(route('acl.approve', [test()->role->id, test()->test_user->id]))
        ->assertForbidden();
});

// Helpers
