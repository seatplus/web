<?php


namespace Seatplus\Web\Tests\Integration;


use Inertia\Testing\Assert;
use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class ServerSettingsTest extends TestCase
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
    public function it_has_users_list()
    {
        $response = $this->actingAs($this->test_user)
            ->get(route('server.settings'));

        $response->assertInertia( fn (Assert $page) => $page->component('Configuration/UserList'));
    }

    /** @test */
    public function it_has_server_scopes()
    {
        $response = $this->actingAs($this->test_user)
            ->get(route('settings.scopes'));

        $response->assertInertia( fn (Assert $page) => $page->component('Configuration/Scopes/OverviewScopeSettings'));

    }

    /** @test */
    public function one_can_impersionate()
    {
        $user_two = Event::fakeFor(fn() => User::factory()->create()) ;

        $response = $this->actingAs($this->test_user)
            ->get(route('impersonate.start', $user_two->id));

        $this->assertAuthenticatedAs($user_two);
    }

    /** @test */
    public function one_can_stop_impersionate()
    {
        $user_two = Event::fakeFor(fn() => User::factory()->create()) ;

       $this->actingAs($this->test_user)
            ->get(route('impersonate.start', $user_two->id));

        $this->assertAuthenticatedAs($user_two);

        $this->actingAs($user_two)
            ->get(route('impersonate.stop'));

        $this->assertAuthenticatedAs($this->test_user);
    }

}
