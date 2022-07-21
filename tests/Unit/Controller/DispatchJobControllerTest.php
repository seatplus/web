<?php


use Seatplus\Eveapi\Jobs\Hydrate\Character\ContactHydrateBatch;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Web\Jobs\ManualDispatchedJob;

beforeEach(function () {
    test()->dispatch_transfer_object = [
        'manual_job' => array_search(ContactHydrateBatch::class, config('web.jobs')),
        'permission' => config('eveapi.permissions.' . Contact::class),
        'required_scopes' => config('eveapi.scopes.character.contacts'),
        'required_corporation_role' => '',
    ];

    Contact::factory()->create([
        'contactable_id' => test()->test_character->character_id,
        'contactable_type' => CharacterInfo::class,
    ]);
});

it('dispatches job', function () {
    $mock = Mockery::mock('overload:' . ManualDispatchedJob::class);
    $mock->shouldReceive('handle')->andReturn(1);
    $mock->shouldReceive('setName')->andReturn($mock);
    $mock->shouldReceive('setJobs')->andReturn($mock);


    $dispatch_transfer_object = test()->dispatch_transfer_object;

    $character_id = test()->test_character->character_id;

    $cache_key = sprintf('%s:%s', $dispatch_transfer_object['manual_job'], $character_id);

    expect(cache($cache_key))->toBeNull();

    $response = test()->actingAs(test()->test_user)
        ->post(route('dispatch.job'), [
            'character_id' => $character_id,
            'dispatch_transfer_object' => $dispatch_transfer_object,
        ])
        ->assertOk();

    test()->assertNotNull(cache($cache_key));
});

test('one get dispatchable character entities', function () {
    updateRefreshTokenWithScopes(test()->test_character->refresh_token, test()->dispatch_transfer_object['required_scopes']);

    expect(test()->test_character->contacts()->count())->toBeGreaterThan(0);

    //expect(test()->test_character->roles->hasRole('roles', test()->dispatch_transfer_object['required_scopes']))->toBeTrue();

    $response = test()->actingAs(test()->test_user)
        ->postJson(route('manual_job.entities'), test()->dispatch_transfer_object);

    $response->assertStatus(200)
        ->assertJson([
            [
                'character_id' => test()->test_character->character_id,
                'name' => test()->test_character->name,
                'batch' => ['state' => 'ready'],
            ],
        ]);
});

test('one get dispatchable corporation entities', function () {
    $dispatch_transfer_object = test()->dispatch_transfer_object;

    expect($dispatch_transfer_object)->toBeArray();

    $dispatch_transfer_object = \Illuminate\Support\Arr::set($dispatch_transfer_object, 'required_corporation_role', 'Director');

    // make test character a director of the corporation
    Event::fakeFor(function () {
        $roles = test()->test_character->roles;
        $roles->roles = ['Director'];
        $roles->save();
    });

    // test character is a director of the corporation
    expect(test()->test_character->roles->hasRole('roles', 'Director'))->toBeTrue();

    // update the refresh token with the required scopes
    updateRefreshTokenWithScopes(test()->test_character->refresh_token, $dispatch_transfer_object['required_scopes']);

    // create contact for the corporation
    Contact::factory()->create([
        'contactable_id' => test()->test_character->corporation->corporation_id,
        'contactable_type' => \Seatplus\Eveapi\Models\Corporation\CorporationInfo::class,
    ]);

    expect(test()->test_character->corporation->contacts()->count())->toBeGreaterThan(0);

    //expect(test()->test_character->roles->hasRole('roles', test()->dispatch_transfer_object['required_scopes']))->toBeTrue();

    $response = test()->actingAs(test()->test_user)
        ->postJson(route('manual_job.entities'), $dispatch_transfer_object);

    $response->assertStatus(200)
        ->assertJson([
            [
                'corporation_id' => test()->test_character->corporation->corporation_id,
                'name' => test()->test_character->corporation->name,
                'batch' => ['state' => 'ready'],
            ],
        ]);
});
