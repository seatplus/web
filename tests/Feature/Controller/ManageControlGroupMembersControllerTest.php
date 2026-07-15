<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

it('denies the configure page to an unauthenticated user', function () {
    test()->get(route('acl.manage', test()->role->id))
        ->assertRedirect();
});

it('denies the configure page without the administrate permission', function () {
    test()->actingAs(test()->test_user)
        ->get(route('acl.manage', test()->role->id))
        ->assertForbidden();
});

it('renders the configure page with the role detail and every join method', function () {
    assignPermissionToTestUser(['administrate access control groups']);

    test()->actingAs(test()->test_user)
        ->get(route('acl.manage', test()->role->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AccessControl/ManageControlGroup')
            ->where('role.name', 'test')
            ->has('role.applies_to')
            ->has('role.eligibility')
            ->has('role.capabilities')
            ->has('joinMethods', 4)
            ->has('joinMethods.0.key')
            ->has('joinMethods.0.label')
        );
});

it('persists the configure form payload — join method, applies-to and eligibility', function () {
    assignPermissionToTestUser(['administrate access control groups']);
    $corporation = CorporationInfo::factory()->create();

    // Exactly the shape RoleConfigSection.transform() posts.
    test()->actingAs(test()->test_user)
        ->post(route('acl.update.opt-in', test()->role->id), [
            'name' => 'Renamed Group',
            'affiliated' => [
                ['entity_id' => $corporation->corporation_id, 'entity_type' => 'corporation', 'affiliation_type' => 'allowed'],
            ],
            'assigned' => [
                ['entity_id' => $corporation->corporation_id, 'entity_type' => 'corporation'],
            ],
        ])
        ->assertRedirect();

    $role = test()->role->fresh();

    expect($role->type)->toBe(RoleType::OPT_IN)
        ->and($role->name)->toBe('Renamed Group')
        ->and($role->affiliations()->where('type', 'allowed')->exists())->toBeTrue();
});
