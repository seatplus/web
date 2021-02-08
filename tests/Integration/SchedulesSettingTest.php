<?php


namespace Seatplus\Web\Tests\Integration;


use ClaudioDekker\Inertia\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Schedules;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class SchedulesSettingTest extends TestCase
{
    public function setUp(): void
    {

        parent::setUp();

        $permission = Permission::findOrCreate('superuser');

        $this->test_user->givePermissionTo($permission);

        // now re-register all the roles and permissions
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }

    /** @test */
    public function it_has_scope_settings()
    {
        $response = $this->actingAs($this->test_user)
            ->get(route('schedules.index'));

        $response->assertInertia( fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesIndex'));
    }

    /** @test */
    public function one_can_create_a_schedule()
    {
        $response = $this->actingAs($this->test_user)
            ->get(route('schedules.create'));

        $response->assertInertia( fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesCreate'));

        $this->assertDatabaseMissing('schedules', ['job' => 'test-job']);

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->postJson(route('schedules.updateOrCreate'), [
                'job' => 'test-job',
                'expression' => 'test-expression'
            ]);

        $response->assertInertia( fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesIndex'));

        $this->assertDatabaseHas('schedules', ['job' => 'test-job']);
    }

    /** @test */
    public function one_can_view_schedule_details()
    {
        $schedule = Schedules::create([
            'job' => 'test-job',
            'expression' => 'test-expression'
        ]);

        $this->assertDatabaseHas('schedules', ['job' => 'test-job']);

        $response = $this->actingAs($this->test_user)
            ->get(route('schedules.details', $schedule->id));

        $response->assertInertia( fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesDetails'));
    }

    /** @test */
    public function one_can_delete_schedule()
    {
        $schedule = Schedules::create([
            'job' => 'test-job',
            'expression' => 'test-expression'
        ]);

        $this->assertDatabaseHas('schedules', ['job' => 'test-job']);

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->delete(route('schedules.delete', $schedule->id));

        $this->assertDatabaseMissing('schedules', ['job' => 'test-job']);

        $response->assertInertia( fn (Assert $page) => $page->component('Configuration/Schedules/SchedulesIndex'));
    }

}
