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

it('denies ApplyToRoleController to unauthenticated user', function () {
    test()->post(route('acl.apply', test()->role->id))
        ->assertRedirect();
});

it('user can apply to on-request role', function () {
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
            ->where('entity_type', User::class)
            ->where('status', RoleMembershipStatus::PENDING->value)
            ->first()
            ?->entity_id
    )->toEqual(test()->test_user->id);
});

it('returns 403 when applying to a manual role', function () {
    assignPermissionToTestUser(['view access control']);

    test()->actingAs(test()->test_user)
        ->post(route('acl.apply', test()->role->id))
        ->assertForbidden();
});

it('returns 403 when applying to an automatic role', function () {
    assignPermissionToTestUser(['administrate access control groups', 'view access control']);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.automatic', test()->role->id), [])
        ->assertRedirect();

    test()->actingAs(test()->test_user)
        ->post(route('acl.apply', test()->role->id))
        ->assertForbidden();
});

it('returns 403 when applying to an opt-in role', function () {
    assignPermissionToTestUser(['administrate access control groups', 'view access control']);

    test()->actingAs(test()->test_user)
        ->postJson(route('acl.update.opt-in', test()->role->id), [])
        ->assertRedirect();

    test()->actingAs(test()->test_user)
        ->post(route('acl.apply', test()->role->id))
        ->assertForbidden();
});
