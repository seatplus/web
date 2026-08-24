<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Resources\RoleRessource;

beforeEach(function () {
    Queue::fake();
});

function makeRole(RoleType $type = RoleType::MANUAL): Role
{
    $role = Role::create(['name' => 'test']);
    $role->update(['type' => $type]);

    // Queried rather than Role::findById()/refresh(): both are declared to return
    // Spatie's model, dropping the concrete seatplus Role and its $type/affiliations().
    return Role::query()->findOrFail($role->id);
}

function resolveRole(Role $role): array
{
    return (new RoleRessource($role->fresh()))->resolve();
}

it('exposes the join-method label and description from the metadata helper', function () {
    $role = makeRole(RoleType::OPT_IN);
    test()->actingAs(test()->test_user);

    $data = resolveRole($role);

    expect($data['type'])->toBe('opt-in')
        ->and($data['type_label'])->toBe('Self-service')
        ->and($data['type_description'])->not->toBeEmpty();
});

it('reports can_moderate for a moderator of an OPT-IN role (regression: old resource only handled on-request)', function () {
    $role = makeRole(RoleType::OPT_IN);

    RoleMembership::create([
        'role_id' => $role->id,
        'entity_type' => User::class,
        'entity_id' => test()->test_user->getKey(),
        'can_moderate' => true,
        'status' => 'active',
    ]);

    test()->actingAs(test()->test_user);

    expect(resolveRole($role)['can_moderate'])->toBeTrue();
});

it('can_edit follows the administrate permission, not the old mismatched string', function () {
    $role = makeRole();
    test()->actingAs(test()->test_user);

    // Capture the pre-grant read: expecting it to be false narrows that expression for
    // the rest of the test, and the grant below does not invalidate the narrowing —
    // so the post-grant assertion has to run on an expression PHPStan has not seen.
    $beforeGrant = resolveRole($role);

    expect($beforeGrant['can_edit'])->toBeFalse();

    assignPermissionToTestUser('administrate access control groups');
    test()->test_user->forgetCachedPermissions();

    expect(resolveRole($role)['can_edit'])->toBeTrue();
});

it('my_status reflects an active membership and enables leave for non-automatic roles', function () {
    $role = makeRole(RoleType::OPT_IN);

    RoleMembership::create([
        'role_id' => $role->id,
        'entity_type' => User::class,
        'entity_id' => test()->test_user->getKey(),
        'status' => 'active',
    ]);

    test()->actingAs(test()->test_user);

    $data = resolveRole($role);

    expect($data['my_status'])->toBe('active')
        ->and($data['can_leave'])->toBeTrue()
        ->and($data['can_join'])->toBeFalse()
        ->and($data['can_apply'])->toBeFalse();
});

it('an automatic role is never moderatable or leavable', function () {
    $role = makeRole(RoleType::AUTOMATIC);

    RoleMembership::create([
        'role_id' => $role->id,
        'entity_type' => User::class,
        'entity_id' => test()->test_user->getKey(),
        'can_moderate' => true,
        'status' => 'active',
    ]);

    test()->actingAs(test()->test_user);

    $data = resolveRole($role);

    expect($data['can_moderate'])->toBeFalse()
        ->and($data['can_leave'])->toBeFalse();
});
