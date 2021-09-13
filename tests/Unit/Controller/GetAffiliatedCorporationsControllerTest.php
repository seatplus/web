<?php


use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;


beforeEach(function () {
    $permission = Permission::findOrCreate('can accept or deny applications');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    test()->app->make(PermissionRegistrar::class)->registerPermissions();
});

it('get affiliated corporations', function () {
    test()->test_character->roles()->update(['roles' => ['Director']]);

    WalletJournal::factory()->count(5)->create([
        'wallet_journable_type' => CorporationInfo::class,
        'wallet_journable_id' => test()->test_character->corporation->corporation_id
    ]);

    $response = test()->actingAs(test()->test_user)
        ->get(route('get.affiliated.corporations', [
            'permission' => 'wallet_journals',
            'corporation_role' => 'Accountant|Junior_Accountant'
        ]));

    $response->assertOk();

    expect($response->original)->toHaveCount(1);
});
