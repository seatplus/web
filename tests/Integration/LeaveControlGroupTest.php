<?php

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\ManualRoleService;
use Seatplus\Auth\Services\Roles\OnRequestRoleService;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test', 'type' => 'on-request']);
    test()->role = Role::find($role->id);

    test()->secondary_user = User::factory()->create();
    test()->secondary_character = test()->secondary_user->characters->first();
});

test('user can leave himself', function () {
    // First create affiliation
    (new ManualRoleService(test()->role))->syncAffiliateManyEntities([
        [test()->test_character->character_id, 'character', 'allowed'],
    ]);

    // Second make test character member
    $service = new ManualRoleService(test()->role);
    $service->addMember(test()->test_user);
    $service->handleMembers();

    expect(test()->test_user->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['view access control']);

    $response = test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [
            'user_id' => test()->test_user->id,
            'role_id' => test()->role->id,
        ]));

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeFalse();
});

test('user can kick other user as superuser', function () {
    // First create affiliation
    (new ManualRoleService(test()->role))->syncAffiliateManyEntities([
        [test()->secondary_character->character_id, 'character', 'allowed'],
    ]);

    // Second make secondary character member
    $service = new ManualRoleService(test()->role);
    $service->addMember(test()->secondary_user);
    $service->handleMembers();

    expect(test()->test_user->hasRole(test()->role))->toBeFalse();
    expect(test()->secondary_user->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['view access control', 'superuser']);

    expect(test()->test_user->can('superuser'))->toBeTrue();

    $response = test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [
            'user_id' => test()->secondary_user->id,
            'role_id' => test()->role->id,
        ]));

    expect(test()->secondary_user->refresh()->hasRole(test()->role))->toBeFalse();
});

test('user can kick other user as moderator', function () {
    // First create affiliation
    (new ManualRoleService(test()->role))->syncAffiliateManyEntities([
        [test()->secondary_character->character_id, 'character', 'allowed'],
    ]);

    // Second make secondary character member
    $service = new ManualRoleService(test()->role);
    $service->addMember(test()->secondary_user);
    $service->handleMembers();
    expect(test()->secondary_user->hasRole(test()->role))->toBeTrue();
    expect(test()->role->role_memberships()->where('can_moderate', true)->doesntExist())->toBeTrue();
    (new OnRequestRoleService(test()->role))->setModerator(test()->test_user);
    expect(test()->role->refresh()->role_memberships()->where('can_moderate', true)->exists())->toBeTrue();

    // Apparently a moderator does not need to be member
    expect(test()->test_user->hasRole(test()->role))->toBeFalse();

    assignPermissionToTestUser(['view access control']);
    expect(test()->test_user->can('superuser'))->toBeFalse();

    $response = test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [
            'user_id' => test()->secondary_user->id,
            'role_id' => test()->role->id,
        ]));

    expect(test()->secondary_user->refresh()->hasRole(test()->role))->toBeFalse();
});

test('user can not kick other user as vanilla user', function () {
    // First create affiliation
    (new ManualRoleService(test()->role))->syncAffiliateManyEntities([
        [test()->secondary_character->character_id, 'character', 'allowed'],
    ]);

    // Second make secondary character member
    $service = new ManualRoleService(test()->role);
    $service->addMember(test()->secondary_user);
    $service->handleMembers();
    expect(test()->secondary_user->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['view access control']);
    expect(test()->test_user->can('superuser'))->toBeFalse();

    $response = test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [
            'user_id' => test()->secondary_user->id,
            'role_id' => test()->role->id,
        ]));

    expect($response->getStatusCode())->toEqual(403);

    expect(test()->secondary_user->refresh()->hasRole(test()->role))->toBeTrue();
});
