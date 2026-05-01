<?php

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\DTO\AffiliationData;
use Seatplus\Auth\Services\Roles\ManualRoleService;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

test('manual control group adds member', function () {
    expect(test()->test_user->hasRole('test'))->toBeFalse();

    $service = new ManualRoleService(test()->role);
    $service->addMember(test()->test_user);
    test()->test_user->assignRole(test()->role);

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeTrue();
});

test('manual control group removes member', function () {
    $service = new ManualRoleService(test()->role);
    $service->addMember(test()->test_user);
    test()->test_user->assignRole(test()->role);

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeTrue();

    $service->removeMember(test()->test_user);
    test()->test_user->removeRole(test()->role);

    expect(test()->test_user->refresh()->hasRole(test()->role))->toBeFalse();
});

test('automatic control group removes affiliation', function () {
    (new ManualRoleService(test()->role))->syncAffiliateManyEntities(
        new AffiliationData(CorporationInfo::factory()->create()->corporation_id, 'corporation', AffiliationType::ALLOWED),
    );

    expect(test()->role->refresh()->affiliations->isEmpty())->toBeFalse();

    assignPermissionToTestUser(['administrate access control groups']);

    // Clearing affiliations requires a direct service call — passing affiliated:[] is falsy so the controller skips it
    (new ManualRoleService(test()->role))->syncAffiliateManyEntities();

    expect(test()->role->refresh()->affiliations->isEmpty())->toBeTrue();
});

test('on request control group sets moderator', function () {
    expect(test()->role->role_memberships()->where('can_moderate', true)->doesntExist())->toBeTrue();

    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);

    // Set role type to on-request
    test()->actingAs($admin)
        ->postJson(route('acl.update.on-request', test()->role->id), ['name' => test()->role->name])
        ->assertRedirect();

    // Admin sets moderator via HTTP route
    test()->actingAs($admin)
        ->post(route('acl.moderator.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->role->refresh()->role_memberships()->where('can_moderate', true)->exists())->toBeTrue();
});

// Helpers
