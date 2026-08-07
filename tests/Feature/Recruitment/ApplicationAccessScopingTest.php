<?php

use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Web\Models\Recruitment\Enlistment;

/**
 * Access scoping for the application review routes — the two properties that must hold for every route
 * behind CheckAffiliationForApplication (get.application, review.application, comment.application and,
 * since #1661, impersonate.recruit):
 *
 *  1. corporation scoping — a recruiter reaches only applicants who applied to a corporation they
 *     recruit for (#1661: impersonation used to skip this entirely);
 *  2. retention — a decision ends access to the applicant's character data, with no grace period
 *     (#1662: the middleware used to have no status filter at all).
 *
 * Deliberately non-superuser throughout: superusers bypass the middleware, so they prove nothing here.
 * The helpers at the bottom are local to this file so it can be run on its own.
 */
beforeEach(function () {
    test()->secondary_user = Event::fakeFor(fn () => User::factory()->create());
    test()->secondary_character = test()->secondary_user->characters->first();

    // createRoleViaHttp() defaults its actor to test()->superuser, which TestCase does not seed.
    test()->superuser = Event::fakeFor(function () {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('superuser'));

        return $user;
    });
});

test('a recruiter cannot impersonate an applicant to a corporation they are not affiliated with', function () {
    openPostingForTestUser();
    applyAsSecondary();

    $application = Application::first();
    expect($application)->status->toBe('open');

    $outsider = recruiterForTheirOwnCorporation();

    test()->actingAs($outsider)
        ->get(route('impersonate.recruit', ['application_id' => $application->id]))
        ->assertForbidden()
        ->assertSessionMissing('impersonation_origin');
});

test('a recruiter cannot inspect an applicant to a corporation they are not affiliated with', function () {
    openPostingForTestUser();
    applyAsSecondary();

    $application = Application::first();

    $outsider = recruiterForTheirOwnCorporation();

    test()->actingAs($outsider)
        ->get(route('get.application', ['application_id' => $application->id]))
        ->assertForbidden();
});

test('rejecting an application ends the recruiter access to the applicant data', function () {
    openPostingForTestUser();
    applyAsSecondary();

    $application = Application::first();

    // While open the affiliated recruiter reaches the full inspection page.
    test()->actingAs(test()->test_user)
        ->get(route('get.application', ['application_id' => $application->id]))
        ->assertOk();

    decideAsTestUser($application, 'rejected');

    expect($application->refresh())->status->toBe('rejected');

    test()->actingAs(test()->test_user)
        ->get(route('get.application', ['application_id' => $application->id]))
        ->assertForbidden();
});

test('accepting an application ends the recruiter access to the applicant data', function () {
    openPostingForTestUser();
    applyAsSecondary();

    $application = Application::first();

    test()->actingAs(test()->test_user)
        ->get(route('get.application', ['application_id' => $application->id]))
        ->assertOk();

    decideAsTestUser($application, 'accepted');

    expect($application->refresh())->status->toBe('accepted');

    test()->actingAs(test()->test_user)
        ->get(route('get.application', ['application_id' => $application->id]))
        ->assertForbidden();
});

test('a decided application can no longer be impersonated', function () {
    openPostingForTestUser();
    applyAsSecondary();

    $application = Application::first();

    decideAsTestUser($application, 'accepted');

    test()->actingAs(test()->test_user)
        ->get(route('impersonate.recruit', ['application_id' => $application->id]))
        ->assertForbidden()
        ->assertSessionMissing('impersonation_origin');
});

test('a decided application can no longer be commented on', function () {
    openPostingForTestUser();
    applyAsSecondary();

    $application = Application::first();

    decideAsTestUser($application, 'rejected');

    test()->actingAs(test()->test_user)
        ->put(route('comment.application', ['application_id' => $application->id]), ['comment' => 'after the fact'])
        ->assertForbidden();
});

test('an intermediate stage decision keeps the application open and inspectable', function () {
    openPostingForTestUser();

    // A second round: the engine only writes a terminal status when accepting on the *final* round, so
    // the first acceptance must leave the application open — and therefore still reachable.
    Enlistment::query()
        ->findOrFail(test()->test_character->corporation->corporation_id)
        ->reviewRounds()
        ->create(['position' => 1, 'label' => 'Senior', 'role_id' => null]);

    applyAsSecondary();

    $application = Application::first();

    decideAsTestUser($application, 'accepted');

    expect($application->refresh())->status->toBe('open');

    test()->actingAs(test()->test_user)
        ->get(route('get.application', ['application_id' => $application->id]))
        ->assertOk();

    // The final round closes it, and access ends with it.
    decideAsTestUser($application, 'accepted');

    expect($application->refresh())->status->toBe('accepted');

    test()->actingAs(test()->test_user)
        ->get(route('get.application', ['application_id' => $application->id]))
        ->assertForbidden();
});

// Helpers

/**
 * Affiliate test_user with their own corporation, give them the recruiter permissions and open a
 * whole-account posting there. PostingController::open seeds a single round (position 0, no control
 * group), so one acceptance settles the application unless more rounds are added.
 */
function openPostingForTestUser(): void
{
    createRoleViaHttp(
        roleName: 'test',
        affiliations: [
            [
                'entity_id' => test()->test_character->corporation->corporation_id,
                'entity_type' => 'corporation',
                'affiliation_type' => 'allowed',
            ],
        ],
        member: test()->test_user,
        permissions: ['can open or close corporations for recruitment', 'can accept or deny applications'],
    );

    test()->actingAs(test()->test_user)
        ->post(route('recruitment.posting.open'), [
            'corporation_id' => test()->test_character->corporation->corporation_id,
            'type' => 'user',
        ])->assertRedirect();

    test()->test_user = test()->test_user->refresh();
    cache()->flush();
}

function applyAsSecondary(): void
{
    test()->actingAs(test()->secondary_user)
        ->post(route('post.application'), [
            'corporation_id' => test()->test_character->corporation->corporation_id,
        ])->assertRedirect();
}

/**
 * A recruiter holding the review permission, but affiliated with their own corporation rather than the
 * one applied to — the cross-corporation case.
 */
function recruiterForTheirOwnCorporation(): User
{
    $outsider = Event::fakeFor(fn () => User::factory()->create());
    $outsiderCorporationId = $outsider->characters->first()->corporation->corporation_id;

    // Guard the fixture: factory corporations are random, and the test is meaningless if they collide.
    expect($outsiderCorporationId)->not->toBe(test()->test_character->corporation->corporation_id);

    createRoleViaHttp(
        roleName: 'outside-recruiter',
        affiliations: [
            [
                'entity_id' => $outsiderCorporationId,
                'entity_type' => 'corporation',
                'affiliation_type' => 'allowed',
            ],
        ],
        member: $outsider,
        permissions: ['can accept or deny applications'],
    );

    cache()->flush();

    return $outsider->refresh();
}

function decideAsTestUser(Application $application, string $decision): void
{
    test()->actingAs(test()->test_user)
        ->post(route('review.application', ['application_id' => $application->id]), [
            'decision' => $decision,
            'explanation' => 'Some reason',
        ])
        ->assertRedirect(route('recruitment.reviews'));
}
