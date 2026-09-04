<?php

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\EsiClient\EsiClient;
use Seatplus\Eveapi\Jobs\Corporation\CorporationInfoJob;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;

beforeEach(function () {
    $permission = Permission::findOrCreate('superuser');

    $this->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
});

it('has scope settings', function () {
    $response = $this->actingAs($this->test_user)
        ->get(route('settings.scopes'));

    $response->assertInertia(fn (Assert $page) => $page->component('Configuration/Scopes/OverviewScopeSettings'));
});

test('one can create sso setting', function () {
    $corporation = CorporationInfo::factory()->make();

    $mock = Mockery::mock(EsiClient::class);
    mockEsiTransport($mock, makeEsiResult((object) $corporation->attributesToArray()));
    app()->instance(EsiClient::class, $mock);

    expect(SsoScopes::query()->where('morphable_id', (string) $corporation->corporation_id)->exists())
        ->toBeFalse();

    $response = $this->actingAs($this->test_user)
        ->post(
            route('create.scopes'),
            [
                'selectedEntities' => [
                    [
                        'corporation_id' => $corporation->corporation_id,
                        'id' => $corporation->corporation_id,
                        'name' => 'Amok.',
                        'category' => 'corporation',
                    ],
                ],
                'selectedScopes' => [
                    'esi-assets.read_assets.v1,esi-universe.read_structures.v1',
                ],
                'type' => 'default',
            ]
        );

    expect(SsoScopes::query()->where('morphable_id', (string) $corporation->corporation_id)->first())
        ->toBeInstanceOf(SsoScopes::class);
});

test('one can delete sso setting', function () {
    $corporation = CorporationInfo::factory()->make();

    expect(SsoScopes::query()->where('morphable_id', (string) $corporation->corporation_id)->exists())
        ->toBeFalse();

    Bus::fake();

    $response = $this->actingAs($this->test_user)
        ->post(
            route('create.scopes'),
            [
                'selectedEntities' => [
                    [
                        'corporation_id' => $corporation->corporation_id,
                        'id' => $corporation->corporation_id,
                        'name' => 'Amok.',
                        'category' => 'corporation',
                    ],
                ],
                'selectedScopes' => [
                    'esi-assets.read_assets.v1,esi-universe.read_structures.v1',
                ],
                'type' => 'default',
            ]
        );

    Bus::assertDispatched(CorporationInfoJob::class);

    expect(SsoScopes::query()->where('morphable_id', (string) $corporation->corporation_id)->first())
        ->toBeInstanceOf(SsoScopes::class);

    $response = $this->actingAs($this->test_user)
        ->delete(route('delete.scopes', $corporation->corporation_id));

    expect(SsoScopes::query()->where('morphable_id', (string) $corporation->corporation_id)->count())->toBe(0);
});

it('leaves the installation-wide entry alone when deleting an entity', function () {
    $corporation = CorporationInfo::factory()->create();

    // The eveapi factory defaults selected_scopes to a JSON string, which the array cast would
    // double-encode — pass a real array.
    SsoScopes::factory()->create([
        'morphable_id' => $corporation->corporation_id,
        'morphable_type' => CorporationInfo::class,
        'type' => 'default',
        'selected_scopes' => ['esi-assets.read_assets.v1'],
    ]);
    SsoScopes::factory()->create([
        'morphable_id' => null,
        'morphable_type' => null,
        'type' => 'global',
        'selected_scopes' => ['esi-skills.read_skills.v1'],
    ]);

    $this->actingAs($this->test_user)
        ->delete(route('delete.scopes', $corporation->corporation_id))
        ->assertRedirect();

    expect(SsoScopes::query()->where('morphable_id', $corporation->corporation_id)->exists())->toBeFalse()
        ->and(SsoScopes::query()->whereNull('morphable_id')->exists())->toBeTrue();
});

it('deletes the installation-wide entry without touching an entity row typed global', function () {
    $corporation = CorporationInfo::factory()->create();

    SsoScopes::factory()->create([
        'morphable_id' => null,
        'morphable_type' => null,
        'type' => 'global',
        'selected_scopes' => ['esi-assets.read_assets.v1'],
    ]);
    // A corporation row carrying the global type — SsoScopes::global() matches on the type alone, so
    // it used to be collateral damage of "delete the global setting".
    SsoScopes::factory()->create([
        'morphable_id' => $corporation->corporation_id,
        'morphable_type' => CorporationInfo::class,
        'type' => 'global',
        'selected_scopes' => ['esi-skills.read_skills.v1'],
    ]);

    $this->actingAs($this->test_user)
        ->delete(route('delete.scopes', null))
        ->assertRedirect();

    expect(SsoScopes::query()->whereNull('morphable_id')->exists())->toBeFalse()
        ->and(SsoScopes::query()->where('morphable_id', $corporation->corporation_id)->exists())->toBeTrue();
});

it('deletes through the model so the sso scope observer fires', function () {
    $corporation = CorporationInfo::factory()->create();

    SsoScopes::factory()->create([
        'morphable_id' => $corporation->corporation_id,
        'morphable_type' => CorporationInfo::class,
        'type' => 'default',
        'selected_scopes' => ['esi-assets.read_assets.v1'],
    ]);

    // seatplus/auth's SsoScopeObserver flushes every user's permission cache on the model's deleted
    // event; a query-builder delete fires no model events at all.
    $deleted = 0;
    Event::listen('eloquent.deleted: '.SsoScopes::class, function () use (&$deleted) {
        $deleted++;
    });

    $this->actingAs($this->test_user)
        ->delete(route('delete.scopes', $corporation->corporation_id))
        ->assertRedirect();

    expect($deleted)->toBe(1);
});

it('does not overwrite an entity row typed global when saving the installation-wide list', function () {
    $corporation = CorporationInfo::factory()->create();

    SsoScopes::factory()->create([
        'morphable_id' => $corporation->corporation_id,
        'morphable_type' => CorporationInfo::class,
        'type' => 'global',
        'selected_scopes' => ['esi-skills.read_skills.v1'],
    ]);

    $this->actingAs($this->test_user)
        ->post(route('create.scopes'), [
            'selectedScopes' => ['esi-assets.read_assets.v1'],
            'type' => 'global',
        ])
        ->assertRedirect();

    $entity = SsoScopes::query()->where('morphable_id', $corporation->corporation_id)->first();

    expect($entity->selected_scopes)->toBe(['esi-skills.read_skills.v1'])
        ->and(SsoScopes::query()->whereNull('morphable_id')->first()->selected_scopes)
        ->toContain('esi-assets.read_assets.v1');
});

test('one can create and delete global sso setting', function () {
    expect(setting('global_sso_scopes'))->toBeNull();

    $response = $this->actingAs($this->test_user)
        ->post(
            route('create.scopes'),
            [
                'selectedScopes' => [
                    'esi-assets.read_assets.v1,esi-universe.read_structures.v1',
                ],
                'type' => 'global',
            ]
        );

    $this->assertNotNull(SsoScopes::global()->first());

    $response = $this->actingAs($this->test_user)
        ->delete(route('delete.scopes', null));
});
