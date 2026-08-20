<?php

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Services\GetAffiliatedIds;

/**
 * The id validation on `dispatch.job` — DispatchIndividualJob::rules(). It authorises the *one* submitted
 * character/corporation id, so the assertions that matter are: an owned id passes, an affiliated id the
 * user does not own passes, and an id outside the user's reach is rejected. Since #1648 each check is a
 * bounded AffiliationResolver probe instead of Rule::in() over a materialised affiliated set, so these
 * cover the tamper guard the materialised list used to provide.
 */
beforeEach(function () {
    test()->dispatch_transfer_object = [
        'manual_job' => 'contacts',
        'permission' => config('eveapi.permissions.'.Contact::class),
        'required_scopes' => config('eveapi.scopes.character.contacts'),
        'required_corporation_role' => [],
    ];

    test()->outsider = Event::fakeFor(fn () => User::factory()->create())->characters->first();
});

it('accepts a dispatch for a character the user owns', function () {
    Bus::fake();

    test()->actingAs(test()->test_user)
        ->postJson(route('dispatch.job'), [
            'character_id' => test()->test_character->character_id,
            'dispatch_transfer_object' => test()->dispatch_transfer_object,
        ])
        ->assertOk();
});

it('accepts a dispatch for an affiliated character the user does not own', function () {
    Bus::fake();

    affiliateTestUserWithEntity(test()->outsider->character_id, 'character');

    test()->actingAs(test()->test_user)
        ->postJson(route('dispatch.job'), [
            'character_id' => test()->outsider->character_id,
            'dispatch_transfer_object' => test()->dispatch_transfer_object,
        ])
        ->assertOk();
});

it('rejects a dispatch for a character the user is not affiliated with', function () {
    test()->actingAs(test()->test_user)
        ->postJson(route('dispatch.job'), [
            'character_id' => test()->outsider->character_id,
            'dispatch_transfer_object' => test()->dispatch_transfer_object,
        ])
        ->assertJsonValidationErrors('character_id');
});

it('rejects a dispatch for a corporation the user is not affiliated with', function () {
    $corporation = CorporationInfo::factory()->create();

    test()->actingAs(test()->test_user)
        ->postJson(route('dispatch.job'), [
            'corporation_id' => $corporation->corporation_id,
            'dispatch_transfer_object' => [...test()->dispatch_transfer_object, 'required_corporation_role' => ['Director']],
        ])
        ->assertJsonValidationErrors('corporation_id');
});

// The two probes the rules are built on, asserted directly. The corporation leg is deliberately not
// driven end-to-end: dispatching a corporation runs into WebJobsRepository reading a non-existent
// `alliance_id` off RefreshToken (and a null-token TypeError when no director token exists) — both
// pre-existing and unrelated to how the id is authorised.
it('reaches an affiliated corporation through the bounded probe the rule uses', function () {
    $corporation = test()->outsider->corporation;

    affiliateTestUserWithEntity($corporation->corporation_id, 'corporation');

    expect((new GetAffiliatedIds(test()->test_user))->coversCorporation(
        $corporation->corporation_id,
        test()->dispatch_transfer_object['permission'],
        ['Director'],
    ))->toBeTrue();
});

it('probes the character id-space for owned, affiliated and unreachable ids', function () {
    $stranger = Event::fakeFor(fn () => User::factory()->create())->characters->first();

    affiliateTestUserWithEntity(test()->outsider->character_id, 'character');

    $service = new GetAffiliatedIds(test()->test_user);
    $permission = test()->dispatch_transfer_object['permission'];

    expect($service->coversCharacter(test()->test_character->character_id, $permission))->toBeTrue()
        ->and($service->coversCharacter(test()->outsider->character_id, $permission))->toBeTrue()
        ->and($service->coversCharacter($stranger->character_id, $permission))->toBeFalse();
});

/**
 * Reach $entityId through a real role — an ALLOWED affiliation on a role granting the dispatch
 * permission, with test_user as its member — so the request resolves it through AffiliationResolver.
 */
function affiliateTestUserWithEntity(int $entityId, string $entityType): void
{
    $superuser = Event::fakeFor(function () {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('superuser'));

        return $user;
    });

    createRoleViaHttp(
        roleName: 'dispatch-validation',
        affiliations: [[
            'entity_id' => $entityId,
            'entity_type' => $entityType,
            'affiliation_type' => AffiliationType::ALLOWED->value,
        ]],
        member: test()->test_user,
        permissions: [test()->dispatch_transfer_object['permission']],
        actor: $superuser,
    );

    // The permission object is cached per user; the fresh role has to be visible to the request.
    cache()->flush();
    test()->test_user = test()->test_user->refresh();
}
