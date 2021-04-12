<?php


namespace Seatplus\Web\Tests\Integration;


use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class CorporationMemberTrackingTest extends  TestCase
{

    public function setUp(): void
    {

        parent::setUp();

        $this->test_character->roles()->update(['roles' => ['']]);
    }

    /** @test */
    public function hasDispatchableJob()
    {

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->get(route('corporation.member_tracking'))
            ->assertForbidden();


        $permission = Permission::findOrCreate('view member tracking');

        $this->test_user->givePermissionTo($permission);

        // now re-register all the roles and permissions
        $this->app->make(PermissionRegistrar::class)->registerPermissions();

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->get(route('corporation.member_tracking'))
            ->assertOk();

        $response->assertInertia( fn (Assert $page) => $page->has('dispatch_transfer_object'));
    }

}
