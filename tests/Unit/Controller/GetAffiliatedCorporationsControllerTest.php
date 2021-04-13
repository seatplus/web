<?php


namespace Seatplus\Web\Tests\Unit\Controller;


use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class GetAffiliatedCorporationsControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $permission = Permission::findOrCreate('can accept or deny applications');

        $this->test_user->givePermissionTo($permission);

        // now re-register all the roles and permissions
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }

    /** @test */
    public function itGetAffiliatedCorporations()
    {
        $this->test_character->roles()->update(['roles' => ['Director']]);

        WalletJournal::factory()->count(5)->create([
            'wallet_journable_type' => CorporationInfo::class,
            'wallet_journable_id' => $this->test_character->corporation->corporation_id
        ]);

        $response = $this->actingAs($this->test_user)
            ->get(route('get.affiliated.corporations', [
                'permission' => 'wallet_journals',
                'corporation_role' => 'Accountant|Junior_Accountant'
            ]));

        $response->assertOk();

        $this->assertCount(1, $response->original);
    }

}