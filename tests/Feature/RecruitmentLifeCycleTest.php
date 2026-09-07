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
use Seatplus\Web\Tests\TestCase;

beforeEach(function () {
    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    $this->secondary_user = Event::fakeFor(fn () => User::factory()->create());

    /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
    $this->superuser = Event::fakeFor(function () {
        $user = User::factory()->create();

        $permission = Permission::findOrCreate('superuser');

        $user->givePermissionTo($permission);

        // now re-register all the roles and permissions

        return $user;
    });

    $this->secondary_character = $this->secondary_user->characters->first();
});

test('user without permission fails to create enlistment', function () {
    $response = $this->actingAs($this->test_user)
        ->post(route('recruitment.posting.open'), [
            'corporation_id' => $this->secondary_character->corporation->corporation_id,
            'type' => 'user',
        ])->assertForbidden();
});

test('user with permission and affiliations succeeds to create enlistment', function () {
    expect(Enlistment::all())->toHaveCount(0);

    createEnlistment($this, $this->superuser);

    expect(Enlistment::all())->toHaveCount(1);
});

test('user with permission and affiliations can delete enlistment', function () {
    createEnlistment($this, $this->superuser);

    $this->assertDatabaseHas('enlistments', [
        'corporation_id' => $this->test_character->corporation->corporation_id,
    ]);

    $this->actingAs($this->test_user)
        ->delete(route('recruitment.posting.close', ['corporation_id' => $this->test_character->corporation->corporation_id]));

    $this->assertDatabaseMissing('enlistments', [
        'corporation_id' => $this->test_character->corporation->corporation_id,
    ]);
});

test('the dashboard no longer lists enlistments (moved to the job portal)', function () {
    createEnlistment($this, $this->superuser);

    $this->actingAs($this->test_user)
        ->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Index')
            ->has('characters')
            ->missing('enlistments')
        );
});

test('secondary user can apply as character', function () {
    createEnlistment($this, $this->superuser, 'character');

    expect($this->secondary_character->refresh()->application)->toBeNull();

    $response = $this->actingAs($this->secondary_user)
        ->post(route('post.application'), [
            'corporation_id' => $this->test_character->corporation->corporation_id,
            'character_id' => $this->secondary_character->character_id,
        ])->assertRedirect();

    $this->assertNotNull($this->secondary_character->refresh()->application);
    expect($this->secondary_character->refresh()->application instanceof Application)->toBeTrue();

    // Pull application
    $response = $this->actingAs($this->secondary_user)
        ->delete(route('delete.character.application', $this->secondary_character->character_id))
        ->assertRedirect();

    expect($this->secondary_character->refresh()->application)->toBeNull();
});

test('secondary user can apply as user', function () {
    createEnlistment($this, $this->superuser, 'user');

    expect($this->secondary_user->refresh()->application)->toBeNull();

    config(['web.config.ONBOARDING' => true]);
    $this->withoutMiddleware(OnboardingMiddleware::class);
    $corporationId = $this->test_character->corporation->corporation_id;

    // the onboarding page attaches the user's open applications per enlistment;
    // before applying, this enlistment carries none
    $this->actingAs($this->secondary_user)
        ->get(route('onboarding'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Onboarding/Index')
                ->where('enlistments', fn ($enlistments) => collect($enlistments)
                    ->firstWhere('corporation_id', $corporationId)['applications'] === [])
        );

    applySecondary($this);

    $this->assertNotNull($this->secondary_user->refresh()->application);
    expect($this->secondary_user->refresh()->application instanceof Application)->toBeTrue();

    // after applying, the same enlistment carries the user's open application
    $this->actingAs($this->secondary_user)
        ->get(route('onboarding'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Onboarding/Index')
                ->where('enlistments', function ($enlistments) use ($corporationId) {
                    $applications = collect($enlistments)->firstWhere('corporation_id', $corporationId)['applications'];

                    return count($applications) === 1
                        && (int) $applications[0]['applicationable_id'] === (int) $this->secondary_user->id
                        && (int) $applications[0]['corporation_id'] === (int) $corporationId;
                })
        );

    // pull application
    $response = $this->actingAs($this->secondary_user)
        ->delete(route('delete.user.application'));

    expect($this->secondary_user->refresh()->application)->toBeNull();
});

test('junior hr handles open user applications', function () {
    createEnlistment($this, $this->superuser);

    $this->test_user = $this->test_user->refresh();

    applySecondary($this);

    // open application

    expect(Application::all())->toHaveCount(1);

    $application = Application::firstOrFail();

    $response = $this->actingAs($this->test_user)
        ->get(route('get.application', ['application_id' => $application->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Recruitment/Review/Application'));

    // Impersonate
    expect($application)->status->toBe('open');

    $response = $this->actingAs($this->test_user)
        ->get(route('impersonate.recruit', ['application_id' => $application->id]))
        ->assertRedirect(route('home'))
        ->assertSessionHas('impersonation_origin', function ($user) {
            return $user->id === $this->test_user->id;
        });

    // Stop Impersonate

    $this->actingAs($this->secondary_user)
        ->withSession(['impersonation_origin' => $this->test_user, 'route' => route('home')])
        ->get(route('impersonate.stop'))
        ->assertRedirect(route('home'))
        ->assertSessionMissing(['impersonation_origin', 'route']);

    // submit review

    \Pest\Laravel\assertDatabaseHas('applications', [
        'applicationable_id' => $this->secondary_user->id,
        'applicationable_type' => User::class,
        'status' => 'open',
    ]);

    $this->actingAs($this->test_user)
        ->post(route('review.application', ['application_id' => $application->id]), [
            'decision' => 'rejected',
            'explanation' => 'Some reason',
        ])
        ->assertRedirect(route('recruitment.reviews'));

    \Pest\Laravel\assertDatabaseHas('applications', [
        'applicationable_id' => $this->secondary_user->id,
        'applicationable_type' => User::class,
        'status' => 'rejected',
    ]);

    expect($this->secondary_user->refresh()->application)->toBeNull();
});

test('junior hr handles open character applications', function () {
    createEnlistment($this, $this->superuser);

    $this->test_user = $this->test_user->refresh();

    applySecondary($this, false);

    // open application
    expect(Application::all())->toHaveCount(1);

    $application = Application::firstOrFail();

    $response = $this->actingAs($this->test_user)
        ->get(route('get.application', ['application_id' => $application->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Recruitment/Review/Application'));

    // submit review

    \Pest\Laravel\assertDatabaseHas('applications', [
        'applicationable_id' => $this->secondary_character->character_id,
        'applicationable_type' => CharacterInfo::class,
        'status' => 'open',
    ]);

    $this->actingAs($this->test_user)
        ->post(route('review.application', ['application_id' => $application->id]), [
            'decision' => 'rejected',
            'explanation' => 'Some reason',
        ])
        ->assertRedirect(route('recruitment.reviews'));

    \Pest\Laravel\assertDatabaseHas('applications', [
        'applicationable_id' => $this->secondary_character->character_id,
        'applicationable_type' => CharacterInfo::class,
        'status' => 'rejected',
    ]);

    expect($this->secondary_character->refresh()->application)->toBeNull();
});

test('recruiter can see corporation applications', function () {
    // Create Enlistment
    createEnlistment($this, $this->superuser, 'character');

    // create Senior Recruiter user

    $recruiter = Event::fakeFor(fn () => User::factory()->create());

    // give user roles

    $role = Role::findByName('test');

    $this->actingAs($this->superuser)
        ->post(route('acl.member.add', [$role->id, $recruiter->id]))
        ->assertRedirect();

    expect($recruiter->refresh()->hasRole($role))->toBeTrue();

    // Apply with secondary user
    applySecondary($this, false);

    expect(Application::all())->toHaveCount(1);

    $application = Application::firstOrFail();

    // Get the test_users Applicaton // /application/{application_id}
    $response = $this->actingAs($recruiter)
        ->get(route('get.application', $application->id))
        ->assertOk();

    // Hit a recruit-specific extended-scope endpoint (contract.details) as an example that a
    // recruiter gets permission to any recruit-specific endpoint.
    $response = $this->actingAs($recruiter)
        ->get(route('contract.details', ['character_id' => $this->secondary_character->character_id, 'contract_id' => 1]))
        ->assertOk();

    // Any other character should be forbidden
    $this->actingAs($recruiter)
        ->get(route('contract.details', ['character_id' => $this->secondary_character->character_id + 1, 'contract_id' => 1]))
        ->assertForbidden();
});

test('recruiter can comment on application', function () {
    // Create Enlistment
    createEnlistment($this, $this->superuser);

    // create Senior Recruiter user

    $recruiter = Event::fakeFor(fn () => User::factory()->create());

    // give user roles

    $role = Role::findByName('test');

    $this->actingAs($this->superuser)
        ->post(route('acl.member.add', [$role->id, $recruiter->id]))
        ->assertRedirect();

    expect($recruiter->refresh()->hasRole($role))->toBeTrue();

    // Apply with secondary user
    applySecondary($this, false);

    expect(Application::all())->toHaveCount(1);

    $application = Application::firstOrFail();

    // Get the test_users Application // /application/{application_id}
    $response = $this->actingAs($recruiter)
        ->get(route('get.application', $application->id))
        ->assertOk();

    $comment = faker()->text;

    $this->actingAs($recruiter)
        ->put(route('comment.application', $application->id), ['comment' => $comment])
        ->assertRedirect();

    expect(ApplicationLogs::all())->toHaveCount(1);

    $response = $this->actingAs($recruiter)
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
    createEnlistment($this, $this->superuser);

    $this->test_user = $this->test_user->refresh();

    applySecondary($this, false);

    // Check if secondary has applied

    \Pest\Laravel\assertDatabaseHas('applications', [
        'applicationable_id' => $this->secondary_character->character_id,
        'applicationable_type' => CharacterInfo::class,
        'status' => 'open',
    ]);

    Queue::fake();
    Queue::assertNothingPushed();

    // first dispatch a update batch
    $this->actingAs($this->test_user)
        ->post(route('dispatch.batch_update', $this->secondary_character->character_id))
        ->assertOk();

    Queue::assertPushedOn('high', UpdateCharacter::class);

    BatchUpdate::firstOrCreate([
        'batchable_id' => $this->secondary_character->character_id,
        'batchable_type' => CharacterInfo::class,
    ]);

    // then get update job information
    $this->actingAs($this->test_user)
        ->get(route('get.batch_update', $this->secondary_character->character_id))
        ->assertJsonFragment(['batchable_id' => $this->secondary_character->character_id]);
});

// Helpers
function applySecondary(TestCase $case, bool $user = true)
{
    $payload = $user
        ? ['corporation_id' => $case->test_character->corporation->corporation_id]
        : ['corporation_id' => $case->test_character->corporation->corporation_id, 'character_id' => $case->secondary_character->character_id];

    $case->actingAs($case->secondary_user)
        ->post(route('post.application'), $payload);
}

function createEnlistment(TestCase $case, User $actor, $type = 'user', string $affiliation = 'allowed')
{
    createRoleViaHttp($case, $actor,
        roleName: 'test',
        affiliations: [
            [
                'entity_id' => $case->test_character->corporation->corporation_id,
                'entity_type' => 'corporation',
                'affiliation_type' => $affiliation,
            ],
        ],
        member: $case->test_user,
        permissions: ['can open or close corporations for recruitment', 'can accept or deny applications'],
    );

    expect($case->test_user->refresh()->hasRole('test'))->toBeTrue();

    // Create Enlistment

    \Pest\Laravel\assertDatabaseMissing('enlistments', [
        'corporation_id' => $case->test_character->corporation->corporation_id,
    ]);

    // Create Enlistment as test user
    $response = $case->actingAs($case->test_user)
        ->post(route('recruitment.posting.open'), [
            'corporation_id' => $case->test_character->corporation->corporation_id,
            'type' => $type,
            'steps' => null,
        ]);

    expect($response)->exception->toBeNull();

    \Pest\Laravel\assertDatabaseHas('enlistments', [
        'corporation_id' => $case->test_character->corporation->corporation_id,
    ]);
}
