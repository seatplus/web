<?php


use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Eveapi\Models\Contracts\ContractItem;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Eveapi\Models\Universe\Type;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    app()->make(PermissionRegistrar::class)->registerPermissions();
});

test('has dispatchable job', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('character.contracts'));

    $response->assertInertia(
        fn (Assert $page) => $page
        ->component('Character/Contract/Index')
        ->has('dispatchTransferObject')
    );
});

test('one get contracts per character', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('character.contracts.details', test()->test_character->character_id))
        ->assertOk();
});

test('one can call transaction endpoint', function () {
    $contract_item = ContractItem::factory()->count(5)->create([
        'contract_id' => Contract::factory(),
    ]);

    $response = test()->actingAs(test()->test_user)
        ->get(route('contract.details', [
            'character_id' => test()->test_character->character_id,
            'contract_id' => 1234,
        ]))
        ->assertOk();

    $response->assertInertia(
        fn (Assert $page) => $page
        ->component('Character/Contract/ContractDetails')
        ->has('contract')
    );
});

it('has watchlist scope', function () {

    // Arrange
    $location = Location::factory()->for(Station::factory(), 'locatable')->create();
    $contract = Contract::factory()->create([
        'start_location_id' => $location->location_id,
        'end_location_id' => $location->location_id,
        'assignee_id' => test()->test_character->character_id,
    ]);

    test()->test_character->contracts()->attach($contract);

    $category = Category::factory()->create();
    $group = Group::factory()->create([
        'category_id' => $category->category_id
    ]);
    $type = Type::factory()->create([
        'group_id' => $group->group_id
    ]);
    ContractItem::factory()->create([
        'contract_id' => $contract->contract_id,
        'type_id' => $type->type_id
    ]);

    $propertyMap = [
        'systems' => [$location->locatable->system->system_id],
        'regions' => [$location->locatable->system->region->region_id],
        'types' => [$type->type_id],
        'groups' => [$group->group_id],
        'categories' => [$category->category_id]
    ];

    foreach ($propertyMap as $key => $value) {
        // Act
        $response = test()->actingAs(test()->test_user)
            ->get(route('character.contracts.details', [
                'character_id' => test()->test_character->character_id,
                //$key => $value
            ]))
            ->assertOk();

        $response->assertJson(
            fn (\Illuminate\Testing\Fluent\AssertableJson $json) => $json
                ->count('data', 1)
                ->has(
                    'data.0',
                    fn ($json) => $json
                        ->where('contract_id', $contract->contract_id)
                        ->etc()
                )
                ->etc()
        );
    }

});
