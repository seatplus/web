<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Auth\Services\Roles\DTO\CriteriaData;
use Seatplus\Auth\Services\Roles\OptInRoleService;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'opt-in role']);
    test()->role = Role::findById($role->id);

    // Dedicated admin so test_user can be the member
    test()->admin = User::factory()->create();
    assignPermission(test()->admin, ['administrate access control groups']);

    test()->test_character = test()->test_character->refresh();
});

it('sets opt-in type and affiliations via HTTP', function () {
    expect(test()->role->type)->toBe(RoleType::MANUAL);

    test()->actingAs(test()->admin)
        ->postJson(route('acl.update.opt-in', test()->role->id), [
            'affiliated' => [
                [
                    'entity_id' => test()->test_character->corporation->corporation_id,
                    'entity_type' => 'corporation',
                    'affiliation_type' => AffiliationType::ALLOWED->value,
                ],
            ],
        ])
        ->assertRedirect(route('acl.detail', test()->role->id));

    expect(test()->role->fresh()->type)->toBe(RoleType::OPT_IN)
        ->and(test()->role->fresh()->affiliations->isNotEmpty())->toBeTrue();
});

it('eligible user can join opt-in role via service and then leave via HTTP', function () {
    // Set criteria via service (uses test_user's corporation)
    $service = new OptInRoleService(test()->role);
    $service->setRoleType(RoleType::OPT_IN);
    $service->addCriteriaForRole(
        new CriteriaData(test()->test_character->corporation->corporation_id, 'corporation')
    );

    // Eligible user joins via service (no HTTP join route for opt-in)
    $optInService = new OptInRoleService(test()->role->fresh());
    $optInService->joinRole(test()->test_user);
    $optInService->handleMembers();

    expect(test()->test_user->fresh()->hasRole(test()->role))->toBeTrue();

    assignPermissionToTestUser(['view access control']);

    // Member leaves via HTTP
    test()->actingAs(test()->test_user)
        ->delete(route('acl.leave', [test()->role->id, test()->test_user->id]))
        ->assertRedirect();

    expect(test()->test_user->fresh()->hasRole(test()->role))->toBeFalse();
});

it('opt-in role rejects moderator assignment', function () {
    test()->actingAs(test()->admin)
        ->postJson(route('acl.update.opt-in', test()->role->id), [])
        ->assertRedirect();

    test()->actingAs(test()->admin)
        ->post(route('acl.moderator.add', [test()->role->id, test()->admin->id]))
        ->assertForbidden();
});
