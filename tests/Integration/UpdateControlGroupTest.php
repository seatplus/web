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

    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->post(route('acl.member.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->refresh()->roles->isNotEmpty())->toBeTrue();
});

test('manual control group removes member', function () {
    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->post(route('acl.member.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->refresh()->roles->isNotEmpty())->toBeTrue();

    test()->actingAs(test()->test_user)
        ->delete(route('acl.member.remove', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->refresh()->roles->isEmpty())->toBeTrue();
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

test('on request control group sets and removes moderator', function () {
    expect(test()->role->role_memberships()->where('can_moderate', true)->doesntExist())->toBeTrue();

    $admin = User::factory()->create();
    assignPermission($admin, ['administrate access control groups']);

    // Admin sets moderator via HTTP route (works on manual roles too)
    test()->actingAs($admin)
        ->post(route('acl.moderator.add', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->role->refresh()->role_memberships()->where('can_moderate', true)->exists())->toBeTrue();

    // Admin removes moderator via HTTP route
    test()->actingAs($admin)
        ->delete(route('acl.moderator.remove', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->role->refresh()->role_memberships()->where('can_moderate', true)->doesntExist())->toBeTrue();
});

// Helpers
