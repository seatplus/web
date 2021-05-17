<?php


namespace Seatplus\Web\Tests\Unit\Controller;

use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class CorporationHistoryControllerTest extends TestCase
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
    public function oneCanCorporationHistoryEndpoint()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('corporation.history', $this->test_character->character_id))
            ->assertOk();
    }


}
