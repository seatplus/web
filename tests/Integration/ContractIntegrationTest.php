<?php


namespace Seatplus\Web\Tests\Unit\Controller;


use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Contracts\ContractItem;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class ContractIntegrationTest extends TestCase
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
    public function hasDispatchableJob()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('character.contracts'));

        $response->assertInertia( fn (Assert $page) => $page
            ->component('Character/Contract/Index')
            ->has('dispatch_transfer_object')
        );
    }

    /** @test */
    public function oneGetContractsPerCharacter()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('character.contracts.details', $this->test_character->character_id))
            ->assertOk();

    }

    /** @test */
    public function oneCanCallTransactionEndpoint()
    {

        $contract_item = ContractItem::factory()->count(5)->create([
            'contract_id' => 1234
        ]);

        $response = $this->actingAs($this->test_user)
            ->get(route('contract.details', [
                'character_id' => $this->test_character->character_id,
                'contract_id' => 1234
            ]))
            ->assertOk();

        $response->assertInertia( fn (Assert $page) => $page
            ->component('Character/Contract/ContractDetails')
            ->has('contract')
        );
    }
}
