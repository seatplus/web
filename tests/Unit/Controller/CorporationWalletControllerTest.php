<?php


namespace Seatplus\Web\Tests\Unit\Controller;


use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class CorporationWalletControllerTest extends TestCase
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
            ->get(route('corporation.wallet'));

        $response->assertInertia( fn (Assert $page) => $page
            ->component('Corporation/Wallets/Wallet')
            ->has('dispatchTransferObject')
        );
    }

    /** @test */
    public function oneCanCallJournalEndpoint()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('corporation.wallet_journal.detail', [
                'corporation_id' => $this->test_character->corporation->corporation_id,
                'division_id' => 1
            ]))
            ->assertOk();
    }

    /** @test */
    public function oneCanCallTransactionEndpoint()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('corporation.wallet_transaction.detail', [
                'corporation_id' => $this->test_character->corporation->corporation_id,
                'division_id' => 1
            ]))
            ->assertOk();
    }

    /** @test */
    public function onGetBallanceRecordsFromLast30Days()
    {
        WalletJournal::factory()->count(1)->create([
            'wallet_journable_id' => $this->test_character->corporation->corporation_id,
            'division' => 1,
            'date' => now()->subDays(29)
        ]);

        $response = $this->actingAs($this->test_user)
            ->get(route('corporation.balance', [
                'corporation_id' => $this->test_character->corporation->corporation_id,
                'division_id' => 1
            ]));

        $response->assertOk();

        $this->assertCount(1, data_get($response->original->toArray(), 'data'));
    }

    /** @test */
    public function onGetBallanceRecordsFromBefore30Days()
    {
        WalletJournal::factory()->count(1)->create([
            'wallet_journable_id' => $this->test_character->corporation->corporation_id,
            'division' => 1,
            'date' => now()->subDays(33)
        ]);

        $response = $this->actingAs($this->test_user)
            ->get(route('corporation.balance', [
                'corporation_id' => $this->test_character->corporation->corporation_id,
                'division_id' => 1
            ]));

        $response->assertOk();

        $this->assertCount(1, data_get($response->original->toArray(), 'data'));
    }
}
