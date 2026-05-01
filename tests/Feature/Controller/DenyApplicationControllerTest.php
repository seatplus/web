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

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

it('denies DenyApplicationController to unauthenticated user', function () {
    test()->delete(route('acl.deny', [test()->role->id, 1]))
        ->assertRedirect();
});

it('non-moderator cannot deny an applicant', function () {
    $other_user = User::factory()->create();

    test()->actingAs($other_user)
        ->delete(route('acl.deny', [test()->role->id, test()->test_user->id]))
        ->assertForbidden();
});

it('moderator can deny an applicant', function () {
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

    expect(
        test()->role->role_memberships()
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->exists()
    )->toBeTrue();

    $moderator = User::factory()->create();
    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);
    test()->actingAs($admin)
        ->post(route('acl.moderator.add', [test()->role->id, $moderator->id]))
        ->assertRedirect();

    test()->actingAs($moderator)
        ->delete(route('acl.deny', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(
        test()->role->role_memberships()
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->exists()
    )->toBeFalse();
});
