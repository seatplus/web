<?php


use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Wallet\WalletJournal;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permission = Permission::findOrCreate('can accept or deny applications');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    app()->make(PermissionRegistrar::class)->registerPermissions();
});

it('get affiliated corporations', function () {
    \Seatplus\Eveapi\Models\Character\CharacterRole::updateOrCreate([
        'character_id' => test()->test_character->character_id,
    ], [
        'roles' => ['Director'],
    ]);

    expect(test()->test_character->roles)
        ->hasRole('roles', 'Director')->toBeTrue()
        ->hasRole('roles', 'Accountant')->toBeTrue();

    WalletJournal::factory()->count(5)->create([
        'wallet_journable_type' => CorporationInfo::class,
        'wallet_journable_id' => test()->test_character->corporation->corporation_id,
    ]);

    expect(CorporationInfo::find(test()->test_character->corporation->corporation_id))
        ->not()->toBeNull()
        ->wallet_journals->toHaveCount(5);

    $response = test()->actingAs(test()->test_user->refresh())
        ->get(route('get.affiliated.corporations', [
            'permission' => 'wallet_journals',
            'corporation_role' => 'Accountant|Junior_Accountant',
            'search' => substr((string) test()->test_character->corporation->name, 5),
        ]));

    $response->assertOk();

    expect($response->original)->toHaveCount(1);
});
