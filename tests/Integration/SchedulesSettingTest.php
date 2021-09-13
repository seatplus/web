<?php


use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Schedules;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

uses(TestCase::class);

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    test()->app->make(PermissionRegistrar::class)->registerPermissions();
});

it('has scope settings', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('schedules.index'));

    $response->assertInertia( fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesIndex'));
});

test('one can create a schedule', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('schedules.create'));

    $response->assertInertia( fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesCreate'));

    test()->assertDatabaseMissing('schedules', ['job' => 'test-job']);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->postJson(route('schedules.updateOrCreate'), [
            'job' => 'test-job',
            'expression' => 'test-expression'
        ]);

    $response->assertInertia( fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesIndex'));

    test()->assertDatabaseHas('schedules', ['job' => 'test-job']);
});

test('one can view schedule details', function () {
    $schedule = Schedules::create([
        'job' => 'test-job',
        'expression' => 'test-expression'
    ]);

    test()->assertDatabaseHas('schedules', ['job' => 'test-job']);

    $response = test()->actingAs(test()->test_user)
        ->get(route('schedules.details', $schedule->id));

    $response->assertInertia( fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesDetails'));
});

test('one can delete schedule', function () {
    $schedule = Schedules::create([
        'job' => 'test-job',
        'expression' => 'test-expression'
    ]);

    test()->assertDatabaseHas('schedules', ['job' => 'test-job']);

    $response = test()->actingAs(test()->test_user)
        ->followingRedirects()
        ->delete(route('schedules.delete', $schedule->id));

    test()->assertDatabaseMissing('schedules', ['job' => 'test-job']);

    $response->assertInertia( fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesIndex'));
});
