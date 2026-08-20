<?php

use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Testing\Fluent\AssertableJson;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;

beforeEach(function () {
    // The real DispatchTransferObject always emits required_corporation_role as an
    // array (empty for character scopes), so the tests must post it that way — an
    // earlier string payload masked the entities endpoint rejecting the array.
    test()->dispatch_transfer_object = [
        'manual_job' => 'contacts',
        'permission' => config('eveapi.permissions.'.Contact::class),
        'required_scopes' => config('eveapi.scopes.character.contacts'),
        'required_corporation_role' => [],
    ];

    Contact::factory()->create([
        'contactable_id' => test()->test_character->character_id,
        'contactable_type' => CharacterInfo::class,
    ]);
});

it('dispatches job', function () {
    // Bus::fake() intercepts the batch the controller dispatches without replacing
    // ManualDispatchedJob itself — a Mockery overload mock would alias the class away
    // for every later test in this process. See tests/Architecture/MockeryOverloadTest.php.
    Bus::fake();

    $dispatch_transfer_object = test()->dispatch_transfer_object;

    $character_id = test()->test_character->character_id;

    $cache_key = sprintf('%s:%s', $dispatch_transfer_object['manual_job'], $character_id);
    $batch_name = sprintf('Manual batch update of %s', $cache_key);

    expect(cache($cache_key))->toBeNull();

    test()->actingAs(test()->test_user)
        ->post(route('dispatch.job'), [
            'character_id' => $character_id,
            'dispatch_transfer_object' => $dispatch_transfer_object,
        ])
        ->assertOk();

    Bus::assertBatched(fn (PendingBatch $batch): bool => $batch->name === $batch_name);

    test()->assertNotNull(cache($cache_key));
});

test('one get dispatchable character entities', function () {
    updateRefreshTokenWithScopes(test()->test_character->refreshToken, test()->dispatch_transfer_object['required_scopes']);

    expect(test()->test_character->contacts()->count())->toBeGreaterThan(0);

    // expect(test()->test_character->roles->hasRole('roles', test()->dispatch_transfer_object['required_scopes']))->toBeTrue();

    $response = test()->actingAs(test()->test_user)
        ->postJson(route('manual_job.entities'), test()->dispatch_transfer_object);

    $response->assertStatus(200)
        ->assertJson(
            fn (AssertableJson $json) => $json->has(
                'data.0',
                fn (AssertableJson $data) => $data->where('character_id', test()->test_character->character_id)
                    ->where('name', test()->test_character->name)
                    ->where('batch.state', 'ready')
                    ->etc()
            )
                ->etc()
        );
});

test('one get dispatchable corporation entities', function () {
    $dispatch_transfer_object = test()->dispatch_transfer_object;

    expect($dispatch_transfer_object)->toBeArray();

    $dispatch_transfer_object = Arr::set($dispatch_transfer_object, 'required_corporation_role', ['Director']);

    // make test character a director of the corporation
    Event::fakeFor(function () {
        $roles = test()->test_character->roles;
        $roles->roles = ['Director'];
        $roles->save();
    });

    // test character is a director of the corporation
    expect(test()->test_character->roles->hasRole('roles', 'Director'))->toBeTrue();

    // update the refresh token with the required scopes
    updateRefreshTokenWithScopes(test()->test_character->refreshToken, $dispatch_transfer_object['required_scopes']);

    // create contact for the corporation
    Contact::factory()->create([
        'contactable_id' => test()->test_character->corporation->corporation_id,
        'contactable_type' => CorporationInfo::class,
    ]);

    expect(test()->test_character->corporation->contacts()->count())->toBeGreaterThan(0);

    $response = test()->actingAs(test()->test_user)
        ->postJson(route('manual_job.entities'), $dispatch_transfer_object);

    $response->assertStatus(200)
        ->assertJson(
            fn (AssertableJson $json) => $json->has(
                'data.0',
                fn (AssertableJson $data) => $data->where('corporation_id', test()->test_character->corporation->corporation_id)
                    ->where('name', test()->test_character->corporation->name)
                    ->where('batch.state', 'ready')
                    ->etc()
            )
                ->etc()
        );
});

test('entities endpoint accepts the real DispatchTransferObject payload', function () {
    updateRefreshTokenWithScopes(test()->test_character->refreshToken, config('eveapi.scopes.character.contacts'));

    // Build the payload the frontend actually sends (serialized DispatchTransferObject)
    // rather than a hand-crafted array, so a drift between the DTO shape and the
    // endpoint's validation (e.g. array vs string role) fails here.
    $dispatch_transfer_object = CreateDispatchTransferObject::new()->create(Contact::class);
    $payload = json_decode(json_encode($dispatch_transfer_object), true);

    expect($payload['required_corporation_role'])->toBeArray();

    test()->actingAs(test()->test_user)
        ->postJson(route('manual_job.entities'), $payload)
        ->assertStatus(200)
        ->assertJson(
            fn (AssertableJson $json) => $json->has(
                'data.0',
                fn (AssertableJson $data) => $data->where('character_id', test()->test_character->character_id)
                    ->where('batch.state', 'ready')
                    ->etc()
            )->etc()
        );
});

test('partitions entities into owned and affiliated by the ownership flag', function () {
    // owned: the test user's own character
    updateRefreshTokenWithScopes(test()->test_character->refreshToken, test()->dispatch_transfer_object['required_scopes']);

    // affiliated: a character the user may manage but does not own
    $affiliated_user = Event::fakeFor(fn () => User::factory()->create());
    $affiliated_character = $affiliated_user->characters->first();
    updateRefreshTokenWithScopes($affiliated_character->refreshToken, test()->dispatch_transfer_object['required_scopes']);

    // Affiliate through a real role covering *both* characters, so the affiliated section proving
    // it drops the owned one is a genuine assertion about the subquery, not about the fixture.
    affiliateTestUserWithCharacters([test()->test_character, $affiliated_character]);

    test()->actingAs(test()->test_user)
        ->postJson(route('manual_job.entities'), [...test()->dispatch_transfer_object, 'ownership' => 'owned'])
        ->assertOk()
        ->assertJsonFragment(['character_id' => test()->test_character->character_id])
        ->assertJsonMissing(['character_id' => $affiliated_character->character_id]);

    test()->actingAs(test()->test_user)
        ->postJson(route('manual_job.entities'), [...test()->dispatch_transfer_object, 'ownership' => 'affiliated'])
        ->assertOk()
        ->assertJsonFragment(['character_id' => $affiliated_character->character_id])
        ->assertJsonMissing(['character_id' => test()->test_character->character_id]);
});

test('the affiliated corporation section drops corporations the user already manages', function () {
    $dispatch_transfer_object = test()->dispatch_transfer_object;
    Arr::set($dispatch_transfer_object, 'required_corporation_role', ['Director']);

    // The affiliated director — a character in another corporation the test user may manage.
    $affiliated_user = Event::fakeFor(fn () => User::factory()->create());
    $affiliated_character = $affiliated_user->characters->first();

    // Factory corporations are random; the test is meaningless if the two collide.
    expect($affiliated_character->corporation->corporation_id)
        ->not->toBe(test()->test_character->corporation->corporation_id);

    // Both characters are directors with the required scopes, so only the owned/affiliated split
    // decides which corporation lands in which section.
    Event::fakeFor(function () use ($affiliated_character) {
        foreach ([test()->test_character, $affiliated_character] as $character) {
            $roles = $character->roles;
            $roles->roles = ['Director'];
            $roles->save();
        }
    });

    updateRefreshTokenWithScopes(test()->test_character->refreshToken, $dispatch_transfer_object['required_scopes']);
    updateRefreshTokenWithScopes($affiliated_character->refreshToken, $dispatch_transfer_object['required_scopes']);

    affiliateTestUserWithCharacters([test()->test_character, $affiliated_character]);

    test()->actingAs(test()->test_user)
        ->postJson(route('manual_job.entities'), [...$dispatch_transfer_object, 'ownership' => 'affiliated'])
        ->assertOk()
        ->assertJsonFragment(['corporation_id' => $affiliated_character->corporation->corporation_id])
        ->assertJsonMissing(['corporation_id' => test()->test_character->corporation->corporation_id]);
});

/**
 * Affiliate the test user with $characters through a real role — an ALLOWED character affiliation on a
 * role granting the dispatch permission, with test_user as its member. That is what the ACL screens
 * produce, so the entities endpoint resolves it through AffiliationResolver; mocking GetAffiliatedIds
 * instead would stub out the very subquery under test.
 *
 * @param  array<int, CharacterInfo>  $characters
 */
function affiliateTestUserWithCharacters(array $characters): void
{
    $superuser = Event::fakeFor(function () {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('superuser'));

        return $user;
    });

    createRoleViaHttp(
        roleName: 'dispatch-auditor',
        affiliations: array_map(fn (CharacterInfo $character): array => [
            'entity_id' => $character->character_id,
            'entity_type' => 'character',
            'affiliation_type' => AffiliationType::ALLOWED->value,
        ], $characters),
        member: test()->test_user,
        permissions: [test()->dispatch_transfer_object['permission']],
        actor: $superuser,
    );

    // The permission object is cached per user; the fresh role has to be visible to the endpoint.
    cache()->flush();
    test()->test_user = test()->test_user->refresh();
}
