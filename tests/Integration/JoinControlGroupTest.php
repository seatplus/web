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

    assignPermissionToTestUser(['view access control', 'manage access control group']);

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
        ->json('POST', route('acl.join'), [
            'user_id' => test()->test_user->id,
            'role_id' => test()->role->id,
        ]);

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

test('superuser can join immediately', function () {
    expect(test()->role->affiliations->isEmpty())->toBeTrue();

    assignPermissionToTestUser(['superuser']);

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

    expect(test()->test_user->roles->isNotEmpty())->toBeFalse();

    $response = test()->actingAs(test()->test_user)
        ->json('POST', route('acl.join'), [
            'user_id' => test()->test_user->id,
            'role_id' => test()->role->id,
        ]);

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeTrue();

    expect(
        test()->role->role_memberships()
            ->where('entity_type', User::class)
            ->where('status', RoleMembershipStatus::ACTIVE->value)
            ->first()
            ->entity_id
    )->toEqual(test()->test_user->id);
});

// Helpers
