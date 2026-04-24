<?php

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Services\Roles\BaseRoleService;
use Seatplus\Auth\Services\Roles\ManualRoleService;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

test('on can update role type', function () {
    expect(test()->test_user->hasRole('test'))->toBeFalse();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual(RoleType::MANUAL);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            'acl' => [
                'type' => 'automatic',
                'affiliations' => [],
                'members' => [],
            ],
        ]);

    expect(test()->role->fresh()->type)->toEqual(RoleType::AUTOMATIC);
});

test('manual control group adds member', function () {
    expect(test()->test_user->hasRole('test'))->toBeFalse();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual(RoleType::MANUAL);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            'acl' => [
                'type' => 'manual',
                'affiliations' => [],
                'members' => [
                    [
                        'id' => test()->test_user->id,
                        'user' => test()->test_user,
                    ],
                ],
            ],
        ]);

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeTrue();
});

test('manual control group removes member', function () {
    $service = new ManualRoleService(test()->role);
    $service->addMember(test()->test_user);
    $service->handleMembers();

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual(RoleType::MANUAL);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            'acl' => [
                'type' => 'manual',
                'affiliations' => [],
                'members' => [],
            ],
        ]);

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeFalse();
});

test('automatic control group adds affiliation', function () {
    expect(test()->role->affiliations->isEmpty())->toBeTrue();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual(RoleType::MANUAL);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            'acl' => [
                'type' => 'automatic',
                'affiliations' => [
                    [
                        'category' => 'corporation',
                        'id' => CorporationInfo::factory()->make()->corporation_id,
                    ],
                ],
                'members' => [],
            ],
        ]);

    expect(test()->role->refresh()->affiliations->isEmpty())->toBeFalse();
});

test('automatic control group removes affiliation', function () {
    expect(test()->role->affiliations->isEmpty())->toBeTrue();

    (new ManualRoleService(test()->role))->syncAffiliateManyEntities([
        [CorporationInfo::factory()->create()->corporation_id, 'corporation', 'allowed'],
    ]);

    test()->assertFalse(test()->role->refresh()->affiliations->isEmpty());

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual(RoleType::MANUAL);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            'acl' => [
                'type' => 'automatic',
                'affiliations' => [],
                'members' => [],
            ],
        ]);

    expect(test()->role->refresh()->affiliations->isEmpty())->toBeTrue();
});

test('on request control group adds and removes moderators', function () {
    expect(test()->role->role_memberships()->where('can_moderate', true)->doesntExist())->toBeTrue();

    assignPermissionToTestUser(['view access control', 'manage access control group']);

    expect(test()->role->type)->toEqual(RoleType::MANUAL);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->json('POST', route('update.acl.affiliations', ['role_id' => test()->role->id]), [
            'acl' => [
                'type' => 'on-request',
                'moderators' => [
                    [
                        'id' => test()->test_user->id,
                    ],
                ],
            ],
        ]);

    // Test if test user is moderator
    expect(test()->role->refresh()->role_memberships()->where('can_moderate', true)->exists())->toBeTrue();
    expect((new BaseRoleService)->for(test()->role->refresh())->canModerate(test()->test_user))->toBeTrue();

    // assert that no affiliations has been created
    expect(test()->role->refresh()->affiliations->isEmpty())->toBeTrue();
});

// Helpers
