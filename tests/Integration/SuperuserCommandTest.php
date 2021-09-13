<?php


use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    // this assures that we have a user->characters relationship
    Event::fakeFor(fn() => CharacterUser::factory()->create(['user_id' => test()->test_user->id]));

});

it('accepts legal user id', function () {

    test()->artisan('seatplus:assign:superuser', ['characterName' => test()->test_user->characters->first()->name])
        ->expectsQuestion('Who should be superadmin?',test()->test_user->id)
        ->expectsQuestion('Do you wish to a continue?', 'y')
        ->assertExitCode(0);
});

it('does not accepts illegal user id', function () {
    test()->assertDatabaseMissing('users', ['id' => test()->test_user->id + 1]);

    test()->artisan('seatplus:assign:superuser', ['characterName' => test()->test_user->characters->first()->name])
        ->expectsQuestion('Who should be superadmin?',test()->test_user->id + 1)
        ->assertExitCode(0);
});

it('creates role', function () {
    test()->assertDatabaseMissing('roles', ['name' => 'Superuser']);

    test()->artisan('seatplus:assign:superuser', ['characterName' => test()->test_user->characters->first()->name])
        ->expectsQuestion('Who should be superadmin?',test()->test_user->id)
        ->expectsQuestion('Do you wish to a continue?', 'y')
        ->assertExitCode(0);

    test()->assertDatabaseHas('roles', ['name' => 'Superuser']);
});

it('assigns super user to user', function () {
    test()->assertFalse(test()->test_user->can('superuser'));

    test()->artisan('seatplus:assign:superuser', ['characterName' => test()->test_user->characters->first()->name])
        ->expectsQuestion('Who should be superadmin?',test()->test_user->id)
        ->expectsQuestion('Do you wish to a continue?', 'y')
        ->assertExitCode(0);

    test()->assertTrue(test()->test_user->refresh()->can('superuser'));
});

test('a second super user can not be assigned', function () {
    //1. give test user superuser
    $role = Role::findOrCreate('Superuser');
    $permission = Permission::findOrCreate('superuser');
    $role->givePermissionTo($permission);
    test()->test_user->assignRole($role);

    $secondary_user = Event::fakeFor(fn() => User::factory()->create()) ;

    test()->artisan('seatplus:assign:superuser', ['characterName' => $secondary_user->main_character])
        ->expectsOutput('Superuser has already been assigned, ask any of the following users to help you out:')
        ->assertExitCode(0);

    test()->assertTrue(test()->test_user->refresh()->can('superuser'));
});
