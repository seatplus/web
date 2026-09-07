<?php

use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;

beforeEach(function () {
    // this assures that we have a user->characters relationship
    Event::fakeFor(fn () => CharacterUser::factory()->create(['user_id' => $this->test_user->id]));
});

it('accepts legal user id', function () {
    $this->artisan('seatplus:assign:superuser', ['characterName' => $this->test_user->characters->first()->name])
        ->expectsQuestion('Who should be superadmin?', $this->test_user->id)
        ->expectsQuestion('Do you wish to a continue?', 'y')
        ->assertExitCode(0);
});

it('does not accepts illegal user id', function () {
    \Pest\Laravel\assertDatabaseMissing('users', ['id' => $this->test_user->id + 1]);

    $this->artisan('seatplus:assign:superuser', ['characterName' => $this->test_user->characters->first()->name])
        ->expectsQuestion('Who should be superadmin?', $this->test_user->id + 1)
        ->assertExitCode(0);
});

it('creates role', function () {
    \Pest\Laravel\assertDatabaseMissing('roles', ['name' => 'Superuser']);

    $this->artisan('seatplus:assign:superuser', ['characterName' => $this->test_user->characters->first()->name])
        ->expectsQuestion('Who should be superadmin?', $this->test_user->id)
        ->expectsQuestion('Do you wish to a continue?', 'y')
        ->assertExitCode(0);

    \Pest\Laravel\assertDatabaseHas('roles', ['name' => 'Superuser']);
});

it('assigns super user to user', function () {
    expect($this->test_user->can('superuser'))->toBeFalse();

    $this->artisan('seatplus:assign:superuser', ['characterName' => $this->test_user->characters->first()->name])
        ->expectsQuestion('Who should be superadmin?', $this->test_user->id)
        ->expectsQuestion('Do you wish to a continue?', 'y')
        ->assertExitCode(0);

    expect($this->test_user->refresh()->can('superuser'))->toBeTrue();
});

test('a second super user can not be assigned', function () {
    // 1. give test user superuser
    $role = Role::findOrCreate('Superuser');
    $permission = Permission::findOrCreate('superuser');
    $role->givePermissionTo($permission);
    $this->test_user->assignRole($role);

    $secondary_user = Event::fakeFor(fn () => User::factory()->create());

    $this->artisan('seatplus:assign:superuser', ['characterName' => $secondary_user->mainCharacter])
        ->expectsOutput('Superuser has already been assigned, ask any of the following users to help you out:')
        ->assertExitCode(0);

    expect($this->test_user->refresh()->can('superuser'))->toBeTrue();
});
