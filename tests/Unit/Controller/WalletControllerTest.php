<?php


namespace Seatplus\Web\Tests\Unit\Controller;


use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class WalletControllerTest extends TestCase
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
            ->get(route('character.wallets'));

        $response->assertInertia( fn (Assert $page) => $page
            ->component('Character/Wallet/Index')
            ->has('dispatch_transfer_object')
        );
    }

    /** @test */
    public function oneCanCallJournalEndpoint()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('character.wallet_journal.detail', $this->test_character->character_id))
            ->assertOk();
    }

    /** @test */
    public function oneCanCallTransactionEndpoint()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('character.wallet_transaction.detail', $this->test_character->character_id))
            ->assertOk();
    }

    /** @test */
    public function onGetBallanceRecordsFromLast30Days()
    {
        WalletJournal::factory()->count(1)->create([
            'wallet_journable_id' => $this->test_character->character_id,
            'date' => now()->subDays(29)
        ]);

        $response = $this->actingAs($this->test_user)
            ->get(route('character.balance', $this->test_character->character_id));

        $response->assertOk();

        $this->assertCount(1, data_get($response->original->toArray(), 'data'));
    }

    /** @test */
    public function onGetBallanceRecordsFromBefore30Days()
    {
        WalletJournal::factory()->count(1)->create([
            'wallet_journable_id' => $this->test_character->character_id,
            'date' => now()->subDays(33)
        ]);

        $response = $this->actingAs($this->test_user)
            ->get(route('character.balance', $this->test_character->character_id));

        $response->assertOk();

        $this->assertCount(1, data_get($response->original->toArray(), 'data'));
    }

    /** @test */
    public function oneCanCallCorporationWalletEndpoint()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('corporation.wallet', [
                'corporation_ids' => [$this->test_character->corporation->corporation_id]
            ]))
            ->assertOk();
    }
}
