<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\Permissions\Role;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

it('denies ListControlGroupsController to unauthenticated user', function () {
    test()->get(route('get.acl'))
        ->assertRedirect();
});

it('returns paginated roles for superuser', function () {
    assignPermissionToTestUser(['superuser']);

    test()->actingAs(test()->test_user)
        ->get(route('get.acl'))
        ->assertOk()
        ->assertJsonFragment(['name' => 'test']);
});

it('returns empty when user has no affiliated roles', function () {
    test()->actingAs(test()->test_user)
        ->get(route('get.acl'))
        ->assertOk()
        ->assertJsonCount(0, 'data');
});
