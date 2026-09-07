<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Queue;
use Seatplus\Auth\Models\Permissions\Role;

beforeEach(function () {
    Queue::fake();

    $role = Role::create(['name' => 'test']);
    $this->role = Role::query()->findOrFail($role->id);
});

it('denies DeleteControlGroupController to unauthenticated user', function () {
    $this->delete(route('acl.delete', $this->role->id))
        ->assertRedirect();
});

it('denies DeleteControlGroupController without permission', function () {
    $this->actingAs($this->test_user)
        ->delete(route('acl.delete', $this->role->id))
        ->assertForbidden();
});

it('admin can delete a control group', function () {
    assignPermission($this->test_user, ['administrate access control groups']);

    \Pest\Laravel\assertDatabaseHas('roles', ['name' => 'test']);

    $this->actingAs($this->test_user)
        ->delete(route('acl.delete', $this->role->id))
        ->assertRedirect(route('acl.groups'));

    \Pest\Laravel\assertDatabaseMissing('roles', ['name' => 'test']);
});
