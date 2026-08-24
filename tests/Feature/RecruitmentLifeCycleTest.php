<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Jobs\Seatplus\UpdateCharacter;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\BatchUpdate;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Recruitment\ApplicationLogs;
use Seatplus\Web\Http\Middleware\OnboardingMiddleware;
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
        ->post(route('recruitment.posting.open'), [
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
        ->delete(route('recruitment.posting.close', ['corporation_id' => test()->test_character->corporation->corporation_id]));

    $this->assertDatabaseMissing('enlistments', [
        'corporation_id' => test()->test_character->corporation->corporation_id,
    ]);
});

test('the dashboard no longer lists enlistments (moved to the job portal)', function () {
    createEnlistment();

    test()->actingAs(test()->test_user)
        ->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Index')
            ->has('characters')
            ->missing('enlistments')
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

    config(['web.config.ONBOARDING' => true]);
    test()->withoutMiddleware(OnboardingMiddleware::class);
    $corporationId = test()->test_character->corporation->corporation_id;

    // the onboarding page attaches the user's open applications per enlistment;
    // before applying, this enlistment carries none
    test()->actingAs(test()->secondary_user)
        ->get(route('onboarding'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Onboarding/Index')
                ->where('enlistments', fn ($enlistments) => collect($enlistments)
                    ->firstWhere('corporation_id', $corporationId)['applications'] === [])
        );

    applySecondary();

    test()->assertNotNull(test()->secondary_user->refresh()->application);
    expect(test()->secondary_user->refresh()->application instanceof Application)->toBeTrue();

    // after applying, the same enlistment carries the user's open application
    test()->actingAs(test()->secondary_user)
        ->get(route('onboarding'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Onboarding/Index')
                ->where('enlistments', function ($enlistments) use ($corporationId) {
                    $applications = collect($enlistments)->firstWhere('corporation_id', $corporationId)['applications'];

                    return count($applications) === 1
                        && (int) $applications[0]['applicationable_id'] === (int) test()->secondary_user->id
                        && (int) $applications[0]['corporation_id'] === (int) $corporationId;
                })
        );

    // pull application
    $response = test()->actingAs(test()->secondary_user)
        ->delete(route('delete.user.application'));

    expect(test()->secondary_user->refresh()->application)->toBeNull();
});

test('junior hr handles open user applications', function () {
    createEnlistment();

    test()->test_user = test()->test_user->refresh();

    applySecondary();

    // open application

    expect(Application::all())->toHaveCount(1);

    $application = Application::firstOrFail();

    $response = test()->actingAs(test()->test_user)
        ->get(route('get.application', ['application_id' => $application->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Recruitment/Review/Application'));

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
        ->assertRedirect(route('recruitment.reviews'));

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

    applySecondary(false);

    // open application
    expect(Application::all())->toHaveCount(1);

    $application = Application::firstOrFail();

    $response = test()->actingAs(test()->test_user)
        ->get(route('get.application', ['application_id' => $application->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Recruitment/Review/Application'));

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
        ->assertRedirect(route('recruitment.reviews'));

    \Pest\Laravel\assertDatabaseHas('applications', [
        'applicationable_id' => test()->secondary_character->character_id,
        'applicationable_type' => CharacterInfo::class,
        'status' => 'rejected',
    ]);

    expect(test()->secondary_character->refresh()->application)->toBeNull();
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

    // Apply with secondary user
    applySecondary(false);

    expect(Application::all())->toHaveCount(1);

    $application = Application::firstOrFail();

    // Get the test_users Applicaton // /application/{application_id}
    $response = test()->actingAs($recruiter)
        ->get(route('get.application', $application->id))
        ->assertOk();

    // Hit a recruit-specific extended-scope endpoint (contract.details) as an example that a
    // recruiter gets permission to any recruit-specific endpoint.
    $response = test()->actingAs($recruiter)
        ->get(route('contract.details', ['character_id' => test()->secondary_character->character_id, 'contract_id' => 1]))
        ->assertOk();

    // Any other character should be forbidden
    test()->actingAs($recruiter)
        ->get(route('contract.details', ['character_id' => test()->secondary_character->character_id + 1, 'contract_id' => 1]))
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

    expect(Application::all())->toHaveCount(1);

    $application = Application::firstOrFail();

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
                ->component('Recruitment/Review/Application')
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
        ->post(route('recruitment.posting.open'), [
            'corporation_id' => test()->test_character->corporation->corporation_id,
            'type' => $type,
            'steps' => null,
        ]);

    expect($response)->exception->toBeNull();

    \Pest\Laravel\assertDatabaseHas('enlistments', [
        'corporation_id' => test()->test_character->corporation->corporation_id,
    ]);
}
