<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\Permissions\Role;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    test()->role = Role::findById($role->id);
});

it('denies DeleteControlGroupController to unauthenticated user', function () {
    test()->delete(route('acl.delete', test()->role->id))
        ->assertRedirect();
});

it('denies DeleteControlGroupController without permission', function () {
    test()->actingAs(test()->test_user)
        ->delete(route('acl.delete', test()->role->id))
        ->assertForbidden();
});

it('admin can delete a control group', function () {
    assignPermissionToTestUser(['administrate access control groups']);

    \Pest\Laravel\assertDatabaseHas('roles', ['name' => 'test']);

    test()->actingAs(test()->test_user)
        ->delete(route('acl.delete', test()->role->id))
        ->assertRedirect(route('acl.groups'));

    \Pest\Laravel\assertDatabaseMissing('roles', ['name' => 'test']);
});
