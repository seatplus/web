<?php

use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Illuminate\Testing\Fluent\AssertableJson;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Jobs\Contacts\AllianceContactJob;
use Seatplus\Eveapi\Jobs\Contacts\AllianceContactLabelJob;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Services\Controller\CreateDispatchTransferObject;

beforeEach(function () {
    // The real DispatchTransferObject always emits required_corporation_role as an
    // array (empty for character scopes), so the tests must post it that way — an
    // earlier string payload masked the entities endpoint rejecting the array.
    $this->dispatch_transfer_object = [
        'manual_job' => 'contacts',
        'permission' => config('eveapi.permissions.'.Contact::class),
        'required_scopes' => config('eveapi.scopes.character.contacts'),
        'required_corporation_role' => [],
    ];

    Contact::factory()->create([
        'contactable_id' => $this->test_character->character_id,
        'contactable_type' => CharacterInfo::class,
    ]);
});

it('dispatches job', function () {
    // Bus::fake() intercepts the batch the controller dispatches without replacing
    // ManualDispatchedJob itself — a Mockery overload mock would alias the class away
    // for every later test in this process. See tests/Architecture/MockeryOverloadTest.php.
    Bus::fake();

    $dispatch_transfer_object = $this->dispatch_transfer_object;

    $character_id = $this->test_character->character_id;

    $cache_key = sprintf('%s:%s', $dispatch_transfer_object['manual_job'], $character_id);
    $batch_name = sprintf('Manual batch update of %s', $cache_key);

    expect(cache($cache_key))->toBeNull();

    $this->actingAs($this->test_user)
        ->post(route('dispatch.job'), [
            'character_id' => $character_id,
            'dispatch_transfer_object' => $dispatch_transfer_object,
        ])
        ->assertOk();

    Bus::assertBatched(fn (PendingBatch $batch): bool => $batch->name === $batch_name);

    $this->assertNotNull(cache($cache_key));
});

test('one get dispatchable character entities', function () {
    updateRefreshTokenWithScopes($this->test_character->refreshToken, $this->dispatch_transfer_object['required_scopes']);

    expect($this->test_character->contacts()->count())->toBeGreaterThan(0);

    // expect($this->test_character->roles->hasRole('roles', $this->dispatch_transfer_object['required_scopes']))->toBeTrue();

    $response = $this->actingAs($this->test_user)
        ->postJson(route('manual_job.entities'), $this->dispatch_transfer_object);

    $response->assertStatus(200)
        ->assertJson(
            fn (AssertableJson $json) => $json->has(
                'data.0',
                fn (AssertableJson $data) => $data->where('character_id', $this->test_character->character_id)
                    ->where('name', $this->test_character->name)
                    ->where('batch.state', 'ready')
                    ->etc()
            )
                ->etc()
        );
});

test('one get dispatchable corporation entities', function () {
    $dispatch_transfer_object = $this->dispatch_transfer_object;

    $dispatch_transfer_object = Arr::set($dispatch_transfer_object, 'required_corporation_role', ['Director']);

    // make test character a director of the corporation
    Event::fakeFor(function () {
        $roles = $this->test_character->roles;
        $roles->roles = ['Director'];
        $roles->save();
    });

    // test character is a director of the corporation
    expect($this->test_character->roles->hasRole('roles', 'Director'))->toBeTrue();

    // update the refresh token with the required scopes
    updateRefreshTokenWithScopes($this->test_character->refreshToken, $dispatch_transfer_object['required_scopes']);

    // create contact for the corporation
    Contact::factory()->create([
        'contactable_id' => $this->test_character->corporation->corporation_id,
        'contactable_type' => CorporationInfo::class,
    ]);

    expect($this->test_character->corporation->contacts()->count())->toBeGreaterThan(0);

    $response = $this->actingAs($this->test_user)
        ->postJson(route('manual_job.entities'), $dispatch_transfer_object);

    $response->assertStatus(200)
        ->assertJson(
            fn (AssertableJson $json) => $json->has(
                'data.0',
                fn (AssertableJson $data) => $data->where('corporation_id', $this->test_character->corporation->corporation_id)
                    ->where('name', $this->test_character->corporation->name)
                    ->where('batch.state', 'ready')
                    ->etc()
            )
                ->etc()
        );
});

test('entities endpoint accepts the real DispatchTransferObject payload', function () {
    updateRefreshTokenWithScopes($this->test_character->refreshToken, config('eveapi.scopes.character.contacts'));

    // Build the payload the frontend actually sends (serialized DispatchTransferObject)
    // rather than a hand-crafted array, so a drift between the DTO shape and the
    // endpoint's validation (e.g. array vs string role) fails here.
    $dispatch_transfer_object = CreateDispatchTransferObject::new()->create(Contact::class);
    $payload = json_decode(json_encode($dispatch_transfer_object), true);

    expect($payload['required_corporation_role'])->toBeArray();

    $this->actingAs($this->test_user)
        ->postJson(route('manual_job.entities'), $payload)
        ->assertStatus(200)
        ->assertJson(
            fn (AssertableJson $json) => $json->has(
                'data.0',
                fn (AssertableJson $data) => $data->where('character_id', $this->test_character->character_id)
                    ->where('batch.state', 'ready')
                    ->etc()
            )->etc()
        );
});

test('partitions entities into owned and affiliated by the ownership flag', function () {
    // owned: the test user's own character
    updateRefreshTokenWithScopes($this->test_character->refreshToken, $this->dispatch_transfer_object['required_scopes']);

    // affiliated: a character the user may manage but does not own
    $affiliated_user = Event::fakeFor(fn () => User::factory()->create());
    $affiliated_character = $affiliated_user->characters->first();
    updateRefreshTokenWithScopes($affiliated_character->refreshToken, $this->dispatch_transfer_object['required_scopes']);

    // No mock: owned resolves from the cached permission object, and the affiliated section is
    // scoped by an AffiliationResolver subquery — so the partition has to be earned with a real
    // role, not asserted by a stubbed id array.
    $this->superuser = Event::fakeFor(function () {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('superuser'));

        return $user;
    });

    // The role covers BOTH corporations, so the resolved affiliated set contains the user's own
    // character too — which is what makes the affiliated section's owned-subtraction load-bearing
    // here rather than incidentally true because the two characters sit in different corps.
    createRoleViaHttp($this, $this->superuser,
        roleName: 'contact-manager',
        affiliations: array_map(fn (int $corporation_id): array => [
            'entity_id' => $corporation_id,
            'entity_type' => 'corporation',
            'affiliation_type' => 'allowed',
        ], [
            $this->test_character->corporation->corporation_id,
            $affiliated_character->corporation->corporation_id,
        ]),
        member: $this->test_user,
        permissions: [$this->dispatch_transfer_object['permission']],
    );

    cache()->flush();

    $this->actingAs($this->test_user)
        ->postJson(route('manual_job.entities'), [...$this->dispatch_transfer_object, 'ownership' => 'owned'])
        ->assertOk()
        ->assertJsonFragment(['character_id' => $this->test_character->character_id])
        ->assertJsonMissing(['character_id' => $affiliated_character->character_id]);

    $this->actingAs($this->test_user)
        ->postJson(route('manual_job.entities'), [...$this->dispatch_transfer_object, 'ownership' => 'affiliated'])
        ->assertOk()
        ->assertJsonFragment(['character_id' => $affiliated_character->character_id])
        ->assertJsonMissing(['character_id' => $this->test_character->character_id]);
});

test('the affiliated corporation section drops corporations the user already manages', function () {
    // The corp-scope counterpart of the partition test above, and the only path that reaches the
    // owned-corporation reject — which is why that reject shipped broken: it plucked corporation_id
    // off character_infos, where it is an accessor over character_affiliations rather than a column.
    $dispatch_transfer_object = $this->dispatch_transfer_object;
    Arr::set($dispatch_transfer_object, 'required_corporation_role', ['Director']);

    $affiliated_user = Event::fakeFor(fn () => User::factory()->create());
    $affiliated_character = $affiliated_user->characters->first();

    // Factory corporations are random; the test says nothing if the two collide.
    expect($affiliated_character->corporation->corporation_id)
        ->not->toBe($this->test_character->corporation->corporation_id);

    // Both characters are directors holding the required scopes, so the owned/affiliated split is
    // the only thing deciding which corporation lands in which section.
    Event::fakeFor(function () use ($affiliated_character) {
        foreach ([$this->test_character, $affiliated_character] as $character) {
            $roles = $character->roles;
            $roles->roles = ['Director'];
            $roles->save();
        }
    });

    updateRefreshTokenWithScopes($this->test_character->refreshToken, $dispatch_transfer_object['required_scopes']);
    updateRefreshTokenWithScopes($affiliated_character->refreshToken, $dispatch_transfer_object['required_scopes']);

    $this->superuser = Event::fakeFor(function () {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('superuser'));

        return $user;
    });

    createRoleViaHttp($this, $this->superuser,
        roleName: 'corporation-contact-manager',
        affiliations: array_map(fn (CharacterInfo $character): array => [
            'entity_id' => $character->character_id,
            'entity_type' => 'character',
            'affiliation_type' => 'allowed',
        ], [$this->test_character, $affiliated_character]),
        member: $this->test_user,
        permissions: [$this->dispatch_transfer_object['permission']],
    );

    cache()->flush();

    $this->actingAs($this->test_user)
        ->postJson(route('manual_job.entities'), [...$dispatch_transfer_object, 'ownership' => 'affiliated'])
        ->assertOk()
        ->assertJsonFragment(['corporation_id' => $affiliated_character->corporation->corporation_id])
        ->assertJsonMissing(['corporation_id' => $this->test_character->corporation->corporation_id]);
});

test('dispatches the alliance contact jobs when the character is in an alliance', function () {
    // Regression: the alliance gate read $refresh_token->alliance_id. RefreshToken defines a
    // corporationId() accessor but nothing for the alliance, so that resolved to null on every
    // token, (int) null was 0, and these two jobs could not dispatch for anybody. An ignore
    // annotation above the line kept it out of sight of static analysis.
    Bus::fake();

    // The factory's alliance_id is fake()->optional(), so it has to be pinned here — otherwise the
    // test passes or fails at random depending on whether the character drew an alliance.
    $alliance_id = 99000042;
    $affiliation = $this->test_character->characterAffiliation;
    $affiliation->alliance_id = $alliance_id;
    $affiliation->save();

    // eveapi's character.contacts scope list includes esi-alliances.read_contacts.v1, which is the
    // scope the alliance branch gates on.
    updateRefreshTokenWithScopes($this->test_character->refreshToken, config('eveapi.scopes.character.contacts'));

    $this->actingAs($this->test_user)
        ->post(route('dispatch.job'), [
            'character_id' => $this->test_character->character_id,
            'dispatch_transfer_object' => $this->dispatch_transfer_object,
        ])
        ->assertOk();

    Bus::assertBatched(function (PendingBatch $batch) use ($alliance_id): bool {
        // getContactJobs() returns chains (arrays of jobs), hence the flatten.
        $alliance_jobs = collect($batch->jobs)
            ->flatten()
            ->filter(fn (object $job): bool => $job instanceof AllianceContactJob || $job instanceof AllianceContactLabelJob);

        return $alliance_jobs->count() === 2
            && $alliance_jobs->every(fn (object $job): bool => $job->allianceId === $alliance_id);
    });
});

test('does not dispatch the alliance contact jobs for a character in no alliance', function () {
    // The other half of the branch: the guard above must still hold, so that fixing the id lookup
    // did not turn "no alliance" into an AllianceContactJob(0).
    Bus::fake();

    $affiliation = $this->test_character->characterAffiliation;
    $affiliation->alliance_id = null;
    $affiliation->save();

    updateRefreshTokenWithScopes($this->test_character->refreshToken, config('eveapi.scopes.character.contacts'));

    $this->actingAs($this->test_user)
        ->post(route('dispatch.job'), [
            'character_id' => $this->test_character->character_id,
            'dispatch_transfer_object' => $this->dispatch_transfer_object,
        ])
        ->assertOk();

    Bus::assertBatched(fn (PendingBatch $batch): bool => collect($batch->jobs)
        ->flatten()
        ->every(fn (object $job): bool => ! $job instanceof AllianceContactJob && ! $job instanceof AllianceContactLabelJob));
});

test('rejects a dispatch for an affiliated character that has no usable token', function () {
    // Affiliation and token availability are independent: coversCharacter() passes on an owned
    // character whether or not a token still exists for it. getConstructedJobs() takes a
    // non-nullable RefreshToken, so the null used to surface as a TypeError (500) rather than as
    // the handled redirect every other rejection in this action returns.
    Bus::fake();

    $character_id = $this->test_character->character_id;

    // A revoked token, which is how this arises in practice.
    $this->test_character->refreshToken->delete();

    $cache_key = sprintf('%s:%s', $this->dispatch_transfer_object['manual_job'], $character_id);

    $this->actingAs($this->test_user)
        ->post(route('dispatch.job'), [
            'character_id' => $character_id,
            'dispatch_transfer_object' => $this->dispatch_transfer_object,
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    Bus::assertNothingBatched();

    // Nothing may be cached for a dispatch that never happened, or the id would be locked out for
    // the hour it takes the entry to expire.
    expect(cache($cache_key))->toBeNull();
});
