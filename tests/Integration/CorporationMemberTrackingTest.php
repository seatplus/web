<?php


namespace Seatplus\Web\Tests\Integration;


use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class CorporationMemberTrackingTest extends  TestCase
{
    /** @test */
    public function isForbiddenIfPermissionIsMissing()
    {

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->get(route('corporation.member_tracking'))
            ->assertForbidden();
    }

    /** @test */
    public function isProtectedByPermission()
    {
        $permission = Permission::findOrCreate('corporation.member_tracking');
        $this->test_user->givePermissionTo($permission);

        // now re-register all the roles and permissions
        $this->app->make(PermissionRegistrar::class)->registerPermissions();

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->get(route('corporation.member_tracking'))
            ->assertOk();
    }

    /** @test */
    public function hasDispatchableJob()
    {
        $permission = Permission::findOrCreate('corporation.member_tracking');
        $this->test_user->givePermissionTo($permission);

        // now re-register all the roles and permissions
        $this->app->make(PermissionRegistrar::class)->registerPermissions();

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->get(route('corporation.member_tracking'));

        $response->assertHasProp('dispatchable_jobs');
    }

}
