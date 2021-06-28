<?php


namespace Seatplus\Web\Tests\Integration;


use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class RouteTest extends TestCase
{
    /** @test */
    public function it_protects_configurations_routes()
    {
        $response = $this->actingAs($this->test_user)
            ->get(route('server.settings'))
            ->assertForbidden();
    }

    /** @test */
    public function it_protects_access_control_routes()
    {
        $response = $this->actingAs($this->test_user)
            ->get(route('acl.groups'))
            ->assertForbidden();
    }

    /** @test */
    public function it_allows_superuser_on_protected_access_control_routes()
    {
        $permission = Permission::findOrCreate('superuser');

        $this->test_user->givePermissionTo($permission);

        // now re-register all the roles and permissions
        $this->app->make(PermissionRegistrar::class)->registerPermissions();

        $response = $this->actingAs($this->test_user)
            ->get(route('acl.groups'))
            ->assertOk();
    }

}
