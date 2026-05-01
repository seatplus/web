<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\DTO\AffiliationData;
use Seatplus\Auth\Services\Roles\ManualRoleService;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test', 'type' => 'on-request']);
    test()->role = Role::find($role->id);

    test()->secondary_user = User::factory()->create();
    test()->secondary_character = test()->secondary_user->characters->first();
});

it('denies LeaveControlGroupController to unauthenticated user', function () {
    test()->delete(route('acl.leave', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();
});

it('user can leave their own on-request role', function () {
    (new ManualRoleService(test()->role))->syncAffiliateManyEntities(
        new AffiliationData(test()->test_character->character_id, 'character', AffiliationType::ALLOWED),
    );

    $service = new ManualRoleService(test()->role);
    $service->addMember(test()->test_user);
    $service->handleMembers();

    expect(test()->test_user->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['view access control']);

    test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeFalse();
});

it('superuser can kick another user', function () {
    (new ManualRoleService(test()->role))->syncAffiliateManyEntities(
        new AffiliationData(test()->secondary_character->character_id, 'character', AffiliationType::ALLOWED),
    );

    $service = new ManualRoleService(test()->role);
    $service->addMember(test()->secondary_user);
    $service->handleMembers();

    expect(test()->secondary_user->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['superuser']);

    test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [test()->role->id, test()->secondary_user->id]))
        ->assertRedirect();

    expect(test()->secondary_user->refresh()->hasRole(test()->role))->toBeFalse();
});

it('moderator can kick another user', function () {
    (new ManualRoleService(test()->role))->syncAffiliateManyEntities(
        new AffiliationData(test()->secondary_character->character_id, 'character', AffiliationType::ALLOWED),
    );

    $service = new ManualRoleService(test()->role);
    $service->addMember(test()->secondary_user);
    $service->handleMembers();

    expect(test()->secondary_user->hasRole(test()->role))->toBeTrue();

    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);
    test()->actingAs($admin)
        ->post(route('acl.moderator.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    assignPermissionToTestUser(['view access control']);

    test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [test()->role->id, test()->secondary_user->id]))
        ->assertRedirect();

    expect(test()->secondary_user->refresh()->hasRole(test()->role))->toBeFalse();
});

it('vanilla user cannot kick another user', function () {
    (new ManualRoleService(test()->role))->syncAffiliateManyEntities(
        new AffiliationData(test()->secondary_character->character_id, 'character', AffiliationType::ALLOWED),
    );

    $service = new ManualRoleService(test()->role);
    $service->addMember(test()->secondary_user);
    $service->handleMembers();

    expect(test()->secondary_user->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['view access control']);

    test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [test()->role->id, test()->secondary_user->id]))
        ->assertForbidden();

    expect(test()->secondary_user->refresh()->hasRole(test()->role))->toBeTrue();
});
