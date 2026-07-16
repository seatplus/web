<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Jobs\Seatplus\UpdateCharacter;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\BatchUpdate;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Recruitment\ApplicationLogs;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Region;
use Seatplus\Eveapi\Models\Universe\System;
use Seatplus\Eveapi\Models\Universe\Type;
use Seatplus\Web\Models\Recruitment\Enlistment;

beforeEach(function () {
    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    test()->secondary_user = Event::fakeFor(fn () => User::factory()->create());

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    test()->superuser = Event::fakeFor(function () {
        $user = User::factory()->create();

        $permission = Permission::findOrCreate('superuser');

        $user->givePermissionTo($permission);

        // now re-register all the roles and permissions

        return $user;
    });

    test()->secondary_character = test()->secondary_user->characters->first();
});

test('user without permission fails to create enlistment', function () {
    $response = test()->actingAs(test()->test_user)
        ->post(route('create.corporation.recruitment'), [
            'corporation_id' => test()->secondary_character->corporation->corporation_id,
            'type' => 'user',
        ])->assertForbidden();
});

test('user with permission and affiliations succeeds to create enlistment', function () {
    expect(Enlistment::all())->toHaveCount(0);

    createEnlistment();

    expect(Enlistment::all())->toHaveCount(1);
});

test('user with permission and affiliations can delete enlistment', function () {
    createEnlistment();

    $this->assertDatabaseHas('enlistments', [
        'corporation_id' => test()->test_character->corporation->corporation_id,
    ]);

    test()->actingAs(test()->test_user)
        ->delete(route('delete.enlistment', ['corporation_id' => test()->test_character->corporation->corporation_id]));

    $this->assertDatabaseMissing('enlistments', [
        'corporation_id' => test()->test_character->corporation->corporation_id,
    ]);
});

test('secondary user can see enlistment', function () {
    createEnlistment();

    // openEnlistments is a deferred prop on the dashboard — resolve it via an Inertia partial reload.
    test()->actingAs(test()->secondary_user)
        ->get(route('home'), [
            'X-Inertia' => 'true',
            'X-Inertia-Partial-Component' => 'Dashboard/Index',
            'X-Inertia-Partial-Data' => 'openEnlistments',
        ])
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Dashboard/Index')
                ->has('openEnlistments', 1)
                ->where('openEnlistments.0.corporation_id', test()->test_character->corporation->corporation_id)
                ->etc()
        );
});

test('secondary user can apply as character', function () {
    createEnlistment('character');

    expect(test()->secondary_character->refresh()->application)->toBeNull();

    $response = test()->actingAs(test()->secondary_user)
        ->post(route('post.application'), [
            'corporation_id' => test()->test_character->corporation->corporation_id,
            'character_id' => test()->secondary_character->character_id,
        ])->assertRedirect();

    test()->assertNotNull(test()->secondary_character->refresh()->application);
    expect(test()->secondary_character->refresh()->application instanceof Application)->toBeTrue();

    // Pull application
    $response = test()->actingAs(test()->secondary_user)
        ->delete(route('delete.character.application', test()->secondary_character->character_id))
        ->assertRedirect();

    expect(test()->secondary_character->refresh()->application)->toBeNull();
});

test('secondary user can apply as user', function () {
    createEnlistment('user');

    expect(test()->secondary_user->refresh()->application)->toBeNull();

    // first check that existing applications does not exist
    test()->actingAs(test()->secondary_user)
        ->get(route('list.existing.applications', test()->test_character->corporation->corporation_id)) // 'corporation_id' => test()->test_character->corporation->corporation_id
        ->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data', 0)
                ->etc()
        );

    applySecondary();

    test()->assertNotNull(test()->secondary_user->refresh()->application);
    expect(test()->secondary_user->refresh()->application instanceof Application)->toBeTrue();

    // then check that existing applications does exist
    test()->actingAs(test()->secondary_user)
        ->get(route('list.existing.applications', test()->test_character->corporation->corporation_id)) // 'corporation_id' => test()->test_character->corporation->corporation_id
        ->assertJson(
            fn (AssertableJson $json) => $json
                ->has('data', 1)
                ->has(
                    'data.0',
                    fn ($json) => $json
                        ->where('applicationable_id', test()->secondary_user->id)
                        ->where('corporation_id', test()->test_character->corporation->corporation_id)
                        ->etc()
                )
                ->etc()
        );

    // pull application
    $response = test()->actingAs(test()->secondary_user)
        ->delete(route('delete.user.application'));

    expect(test()->secondary_user->refresh()->application)->toBeNull();
});

test('senior hr sees recruitment component', function () {
    expect(test()->test_user->can('superuser'))->toBeFalse();

    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.recruitment'))
        ->assertForbidden();

    assignPermissionToTestUser(['can open or close corporations for recruitment']);

    test()->actingAs(test()->test_user->refresh())
        ->get(route('corporation.recruitment'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Corporation/Recruitment/RecruitmentIndex'));
});

test('junior hr sees recruitment component', function () {
    createEnlistment();

    // First remove all roles from the user
    test()->test_user->syncRoles([]);
    expect(test()->test_user->roles)->toBeEmpty();

    // Remove all Permissions
    test()->test_user->syncPermissions([]);
    expect(test()->test_user->permissions)->toBeEmpty();

    $response = test()->actingAs(test()->test_user)
        ->get(route('corporation.recruitment'))
        ->assertForbidden();

    assignPermissionToTestUser(['can accept or deny applications']);

    expect(test()->test_user->characters)->toHaveCount(1)
        ->and(test()->test_user->characters->first()->character_id)->toBe(test()->test_character->character_id)
        ->and(test()->test_user->can('can open or close corporations for recruitment'))->toBeFalse()
        ->and(test()->test_user->can('superuser'))->toBeFalse()
        ->and(test()->test_user->can('can open or close corporations for recruitment'))->toBeFalse()
        ->and(test()->actingAs(test()->test_user->refresh())->get(route('corporation.recruitment')))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Corporation/Recruitment/RecruitmentIndex')
                ->has('enlistments', 1)
        );
});

test('junior hr handles open user applications', function () {
    createEnlistment();

    test()->test_user = test()->test_user->refresh();

    test()->actingAs(test()->test_user)
        ->get(route('open.corporation.applications', [test()->test_character->corporation->corporation_id, 0]))
        ->assertJsonCount(0, 'data');

    applySecondary();

    test()->actingAs(test()->test_user)
        ->get(route('open.corporation.applications', [test()->test_character->corporation->corporation_id, 0]))
        ->assertJsonCount(1, 'data');

    // open application

    expect(Application::all())->toHaveCount(1)
        ->first()->id->toBeString();

    $application = Application::first();

    $response = test()->actingAs(test()->test_user)
        ->get(route('get.application', ['application_id' => $application->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Corporation/Recruitment/Application'));

    // Impersonate
    expect($application)->status->toBe('open');

    $response = test()->actingAs(test()->test_user)
        ->get(route('impersonate.recruit', ['application_id' => $application->id]))
        ->assertRedirect(route('home'))
        ->assertSessionHas('impersonation_origin', function ($user) {
            return $user->id === test()->test_user->id;
        });

    // Stop Impersonate

    test()->actingAs(test()->secondary_user)
        ->withSession(['impersonation_origin' => test()->test_user, 'route' => route('home')])
        ->get(route('impersonate.stop'))
        ->assertRedirect(route('home'))
        ->assertSessionMissing(['impersonation_origin', 'route']);

    // submit review

    \Pest\Laravel\assertDatabaseHas('applications', [
        'applicationable_id' => test()->secondary_user->id,
        'applicationable_type' => User::class,
        'status' => 'open',
    ]);

    test()->actingAs(test()->test_user)
        ->post(route('review.application', ['application_id' => $application->id]), [
            'decision' => 'rejected',
            'explanation' => 'Some reason',
        ])
        ->assertRedirect(route('corporation.recruitment'));

    \Pest\Laravel\assertDatabaseHas('applications', [
        'applicationable_id' => test()->secondary_user->id,
        'applicationable_type' => User::class,
        'status' => 'rejected',
    ]);

    expect(test()->secondary_user->refresh()->application)->toBeNull();
});

test('junior hr handles open character applications', function () {
    createEnlistment();

    test()->test_user = test()->test_user->refresh();

    test()->actingAs(test()->test_user)
        ->get(route('open.corporation.applications', [test()->test_character->corporation->corporation_id, 0]))
        ->assertJsonCount(0, 'data');

    applySecondary(false);

    $response = test()->actingAs(test()->test_user)
        ->get(route('open.corporation.applications', [test()->test_character->corporation->corporation_id, 0]))
        ->assertJsonCount(1, 'data');

    // open application
    expect(Application::all())->toHaveCount(1)
        ->first()->id->toBeString();

    $application = Application::first();

    $response = test()->actingAs(test()->test_user)
        ->get(route('get.application', ['application_id' => $application->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Corporation/Recruitment/Application'));

    // submit review

    \Pest\Laravel\assertDatabaseHas('applications', [
        'applicationable_id' => test()->secondary_character->character_id,
        'applicationable_type' => CharacterInfo::class,
        'status' => 'open',
    ]);

    test()->actingAs(test()->test_user)
        ->post(route('review.application', ['application_id' => $application->id]), [
            'decision' => 'rejected',
            'explanation' => 'Some reason',
        ])
        ->assertRedirect(route('corporation.recruitment'));

    \Pest\Laravel\assertDatabaseHas('applications', [
        'applicationable_id' => test()->secondary_character->character_id,
        'applicationable_type' => CharacterInfo::class,
        'status' => 'rejected',
    ]);

    expect(test()->secondary_character->refresh()->application)->toBeNull();
});

test('junior h r can see shitlist', function () {
    createEnlistment();

    test()->test_user = test()->test_user->refresh();

    // Create SSO Setting

    // Give Test User required scope

    // Test that test user is not on shitlist
    test()->actingAs(test()->test_user)
        ->get(route('open.corporation.applications', [test()->test_character->corporation->corporation_id, 0]))
        ->assertJsonCount(0, 'data');

    applySecondary();

    test()->actingAs(test()->test_user)
        ->get(route('open.corporation.applications', [test()->test_character->corporation->corporation_id, 0]))
        ->assertJsonCount(1, 'data');
});

test('senior hr can setup watchlist', function () {
    createEnlistment();

    test()->actingAs(test()->test_user->refresh())
        ->get(route('edit.enlistment', test()->test_character->corporation->corporation_id))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Corporation/Recruitment/Configuration/Index')
                ->has('enlistment')
                ->has(
                    'watched',
                    fn (Assert $prop) => $prop
                        ->has('systems', 0)
                        ->has('regions', 0)
                        ->has('items', 0)
                )
        );

    // create system
    $system = System::factory()->create();

    // watchlist system
    $response = test()->actingAs(test()->test_user->refresh())
        ->followingRedirects()
        ->post(route('update.watchlist', test()->test_character->corporation->corporation_id), [
            'systems' => [
                (object) [
                    'id' => $system->system_id,
                ],
            ],
        ])
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Corporation/Recruitment/Configuration/Index')
                ->has(
                    'watched',
                    fn (Assert $prop) => $prop
                        ->has('systems', 1, fn (Assert $prop) => $prop->where('id', $system->system_id)->etc())
                        ->etc()
                )
                ->etc()
        );

    // add region
    $region = Region::factory()->create();
    test()->actingAs(test()->test_user->refresh())
        ->followingRedirects()
        ->post(route('update.watchlist', test()->test_character->corporation->corporation_id), [
            'systems' => [
                (object) [
                    'id' => $system->system_id,
                ],
            ],
            'regions' => [
                (object) [
                    'id' => $region->region_id,
                ],
            ],
        ])
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Corporation/Recruitment/Configuration/Index')
                ->has(
                    'watched',
                    fn (Assert $prop) => $prop
                        ->has('systems', 1, fn (Assert $prop) => $prop->where('id', $system->system_id)->etc())
                        ->has('regions', 1, fn (Assert $prop) => $prop->where('id', $region->region_id)->etc())
                        ->etc()
                )
                ->etc()
        );

    // add type and don't submit new system or region,
    $group = Group::factory()->create(['category_id' => Category::factory()]);
    $type = Type::factory()->create(['group_id' => $group]);
    test()->actingAs(test()->test_user->refresh())
        ->followingRedirects()
        ->post(route('update.watchlist', test()->test_character->corporation->corporation_id), [
            'items' => [
                [
                    // only watchable_id and type is required
                    'watchable_id' => $type->type_id,
                    'watchable_type' => Type::class,
                ],
            ],
        ])->assertInertia(
            fn (Assert $page) => $page
                ->component('Corporation/Recruitment/Configuration/Index')
                ->has(
                    'watched',
                    fn (Assert $prop) => $prop
                    // we expect no change for watchlisted systems and regions
                        ->has('systems', 1, fn (Assert $prop) => $prop->where('id', $system->system_id)->etc())
                        ->has('regions', 1, fn (Assert $prop) => $prop->where('id', $region->region_id)->etc())
                        ->has('items', 1, fn (Assert $prop) => $prop->where('watchable_id', $type->type_id)->etc())
                        ->etc()
                )
                ->etc()
        );

    test()->actingAs(test()->test_user->refresh())
        ->followingRedirects()
        ->post(route('update.watchlist', test()->test_character->corporation->corporation_id), [
            'items' => [
                [
                    // only watchable_id and type is required
                    'watchable_id' => $type->group_id,
                    'watchable_type' => Group::class,
                ],
            ],
        ])->assertInertia(
            fn (Assert $page) => $page
                ->component('Corporation/Recruitment/Configuration/Index')
                ->has(
                    'watched',
                    fn (Assert $prop) => $prop
                    // we expect no change for watchlisted systems and regions
                    // however we expect the type previously set to be removed
                        ->has('items', 1, fn (Assert $prop) => $prop->where('watchable_id', $type->group_id)->etc())
                        ->etc()
                )
                ->etc()
        );

    test()->actingAs(test()->test_user->refresh())
        ->followingRedirects()
        ->post(route('update.watchlist', test()->test_character->corporation->corporation_id), [
            'items' => [
                [
                    // only watchable_id and type is required
                    'watchable_id' => $type->group_id,
                    'watchable_type' => Group::class,
                ],
                [
                    // only watchable_id and type is required
                    'watchable_id' => $type->group->category_id,
                    'watchable_type' => Category::class,
                ],
            ],
        ])->assertInertia(
            fn (Assert $page) => $page
                ->component('Corporation/Recruitment/Configuration/Index')
                ->has(
                    'watched',
                    fn (Assert $prop) => $prop
                    // we expect no change for watchlisted systems and regions
                        ->has('items', 2)
                        ->etc()
                )
                ->etc()
        );
});

test('recruiter can see corporation applications', function () {
    // Create Enlistment
    createEnlistment('character');

    // create Senior Recruiter user

    $recruiter = Event::fakeFor(fn () => User::factory()->create());

    // give user roles

    $role = Role::findByName('test');

    test()->actingAs(test()->superuser)
        ->post(route('acl.member.add', [$role->id, $recruiter->id]))
        ->assertRedirect();

    expect($recruiter->refresh()->hasRole($role))->toBeTrue();

    // get list with open appliactions
    $response = test()->actingAs($recruiter)
        ->get(route('open.corporation.applications', [test()->test_character->corporation->corporation_id, 0]))
        ->assertOk();

    // get list with closed applications
    test()->actingAs($recruiter)
        ->get(route('closed.corporation.applications', test()->test_character->corporation->corporation_id))
        ->assertOk();

    // Apply with secondary user
    applySecondary(false);

    expect(Application::all())->toHaveCount(1)
        ->first()->id->toBeString();

    $application = Application::first();

    // Get the test_users Applicaton // /application/{application_id}
    $response = test()->actingAs($recruiter)
        ->get(route('get.application', $application->id))
        ->assertOk();

    // Hit a recruit-specific extended-scope endpoint (corporation.history) as an
    // example that a recruiter gets permission to any recruit-specific endpoint.
    $response = test()->actingAs($recruiter)
        ->get(route('corporation.history', test()->secondary_character->character_id))
        ->assertOk();

    // Any other character should be forbidden
    test()->actingAs($recruiter)
        ->get(route('corporation.history', test()->secondary_character->character_id + 1))
        ->assertForbidden();
});

test('recruiter can comment on application', function () {
    // Create Enlistment
    createEnlistment();

    // create Senior Recruiter user

    $recruiter = Event::fakeFor(fn () => User::factory()->create());

    // give user roles

    $role = Role::findByName('test');

    test()->actingAs(test()->superuser)
        ->post(route('acl.member.add', [$role->id, $recruiter->id]))
        ->assertRedirect();

    expect($recruiter->refresh()->hasRole($role))->toBeTrue();

    // Apply with secondary user
    applySecondary(false);

    expect(Application::all())->toHaveCount(1)
        ->first()->id->toBeString();

    $application = Application::first();

    // Get the test_users Application // /application/{application_id}
    $response = test()->actingAs($recruiter)
        ->get(route('get.application', $application->id))
        ->assertOk();

    $comment = faker()->text;

    test()->actingAs($recruiter)
        ->put(route('comment.application', $application->id), ['comment' => $comment])
        ->assertRedirect();

    expect(ApplicationLogs::all())->toHaveCount(1);

    $response = test()->actingAs($recruiter)
        ->get(route('get.application', $application->id))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Corporation/Recruitment/Application')
                ->has(
                    'application',
                    fn (Assert $page) => $page
                        ->has(
                            'log_entries',
                            1,
                            fn (Assert $page) => $page
                                ->where('comment', $comment)
                                ->has(
                                    'causer',
                                    fn (Assert $page) => $page
                                        ->where('main_character_id', $recruiter->main_character_id)
                                        ->etc()
                                )
                                ->etc()
                        )
                        ->etc()
                )
                ->etc()
        );
});

test('junior hr can dispatch update batch and get status', function () {
    createEnlistment();

    test()->test_user = test()->test_user->refresh();

    applySecondary(false);

    // Check if secondary has applied

    \Pest\Laravel\assertDatabaseHas('applications', [
        'applicationable_id' => test()->secondary_character->character_id,
        'applicationable_type' => CharacterInfo::class,
        'status' => 'open',
    ]);

    Queue::fake();
    Queue::assertNothingPushed();

    // first dispatch a update batch
    test()->actingAs(test()->test_user)
        ->post(route('dispatch.batch_update', test()->secondary_character->character_id))
        ->assertOk();

    Queue::assertPushedOn('high', UpdateCharacter::class);

    BatchUpdate::firstOrCreate([
        'batchable_id' => test()->secondary_character->character_id,
        'batchable_type' => CharacterInfo::class,
    ]);

    // then get update job information
    test()->actingAs(test()->test_user)
        ->get(route('get.batch_update', test()->secondary_character->character_id))
        ->assertJsonFragment(['batchable_id' => test()->secondary_character->character_id]);
});

it('returns activity log entries for closed applications', function () {
    $application = Event::fakeFor(fn () => Application::factory()->create([
        'id' => Str::uuid(),
        'status' => 'rejected',
    ]));

    $application->logEntries()->create([
        'causer_type' => CharacterInfo::class,
        'causer_id' => test()->test_character->character_id,
        'type' => faker()->randomElement(['decision', 'comment']),
        'comment' => faker()->text,
    ]);

    assignPermissionToTestUser('superuser');

    $application = $application->refresh();

    test()->actingAs(test()->test_user)
        ->get(route('get.activity.log', $application->id))
        ->assertJson(
            fn (AssertableJson $json) => $json->where('id', $application->id)
                ->where('status', 'rejected')
                ->where(
                    'log_entries',
                    fn (Collection $collection) => Arr::has($collection->first(), 'causer')
                )
                ->etc()
        );
});

// Helpers
function applySecondary(bool $user = true)
{
    $payload = $user
        ? ['corporation_id' => test()->test_character->corporation->corporation_id]
        : ['corporation_id' => test()->test_character->corporation->corporation_id, 'character_id' => test()->secondary_character->character_id];

    test()->actingAs(test()->secondary_user)
        ->post(route('post.application'), $payload);
}

function createEnlistment($type = 'user', string $affiliation = 'allowed')
{
    createRoleViaHttp(
        roleName: 'test',
        affiliations: [
            [
                'entity_id' => test()->test_character->corporation->corporation_id,
                'entity_type' => 'corporation',
                'affiliation_type' => $affiliation,
            ],
        ],
        member: test()->test_user,
        permissions: ['can open or close corporations for recruitment', 'can accept or deny applications'],
    );

    expect(test()->test_user->refresh()->hasRole('test'))->toBeTrue();

    // Create Enlistment

    \Pest\Laravel\assertDatabaseMissing('enlistments', [
        'corporation_id' => test()->test_character->corporation->corporation_id,
    ]);

    // Create Enlistment as test user
    $response = test()->actingAs(test()->test_user)
        ->post(route('create.corporation.recruitment'), [
            'corporation_id' => test()->test_character->corporation->corporation_id,
            'type' => $type,
            'steps' => null,
        ]);

    expect($response)->exception->toBeNull();

    \Pest\Laravel\assertDatabaseHas('enlistments', [
        'corporation_id' => test()->test_character->corporation->corporation_id,
    ]);
}
