<?php

use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Services\GetAffiliatedIds;

/**
 * The two primitives the last two materialising consumers were moved onto:
 *  - constrainToAffiliatedExcludingOwned() replaces `array_diff(get(...), ownedCharacterIds())` in
 *    DispatchJobController::getEntities() (#1654);
 *  - affiliationCacheKey() replaces hashing a resolved id array to key a cache in
 *    GetRecruitIdsService (#1655).
 * Both are exercised against real roles/affiliations, since a mock would stub out the very SQL
 * composition that is the point of the change.
 */
beforeEach(function () {
    test()->permission = 'can accept or deny applications';

    // createRoleViaHttp() defaults its actor to test()->superuser, which TestCase does not seed.
    test()->superuser = Event::fakeFor(function () {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('superuser'));

        return $user;
    });

    test()->outsider = Event::fakeFor(fn () => User::factory()->create())->characters->first();
});

it('constrains a character column to the affiliated set minus the user own characters', function () {
    affiliateTestUserWithEntities([
        entityAffiliation(test()->test_character->character_id, 'character'),
        entityAffiliation(test()->outsider->character_id, 'character'),
    ]);

    // Both characters are in the affiliated set...
    expect((new GetAffiliatedIds(test()->test_user))->get(test()->permission))
        ->toContain(test()->test_character->character_id)
        ->toContain(test()->outsider->character_id);

    // ...but the "affiliated, not mine" partition keeps only the one the user does not own.
    $query = CharacterInfo::query();
    (new GetAffiliatedIds(test()->test_user))
        ->constrainToAffiliatedExcludingOwned($query, 'character_id', test()->permission);

    expect($query->pluck('character_id')->all())
        ->toContain(test()->outsider->character_id)
        ->not->toContain(test()->test_character->character_id);
});

it('refuses a column outside the character id-space', function () {
    expect(fn () => (new GetAffiliatedIds(test()->test_user))->constrainToAffiliatedExcludingOwned(
        CorporationInfo::query(),
        'corporation_id',
        test()->permission,
    ))->toThrow(InvalidArgumentException::class);
});

it('keys the affiliation cache on the inputs, so a growing affiliated set does not move the key', function () {
    $corporation_id = test()->outsider->corporation->corporation_id;

    affiliateTestUserWithEntities([entityAffiliation($corporation_id, 'corporation')]);

    $service = new GetAffiliatedIds(test()->test_user);
    $key = $service->affiliationCacheKey(test()->permission, 'Director');

    expect($service->affiliationCacheKey(test()->permission, 'Director'))->toBe($key);

    // A newcomer joining the affiliated corporation grows the resolved set — which is exactly what
    // the old output-hashed key tracked, and what an input-derived key deliberately does not.
    $newcomer = Event::fakeFor(fn () => User::factory()->create())->characters->first();
    Event::fakeFor(fn () => $newcomer->characterAffiliation->update(['corporation_id' => $corporation_id]));

    expect((new GetAffiliatedIds(test()->test_user))->get(test()->permission, 'Director'))
        ->toContain($newcomer->character_id)
        ->and($service->affiliationCacheKey(test()->permission, 'Director'))->toBe($key);
});

it('moves the affiliation cache key when the user roles change', function () {
    test()->actingAs(test()->test_user);

    $key = (new GetAffiliatedIds(test()->test_user))->affiliationCacheKey(test()->permission, 'Director');

    affiliateTestUserWithEntities([entityAffiliation(test()->outsider->character_id, 'character')]);

    expect((new GetAffiliatedIds(test()->test_user))->affiliationCacheKey(test()->permission, 'Director'))
        ->not->toBe($key);
});

/**
 * @return array{entity_id: int, entity_type: string, affiliation_type: string}
 */
function entityAffiliation(int $entityId, string $entityType): array
{
    return [
        'entity_id' => $entityId,
        'entity_type' => $entityType,
        'affiliation_type' => AffiliationType::ALLOWED->value,
    ];
}

/**
 * Grant the test user the permission under test through a real role carrying $affiliations, then hand
 * the session back to them — createRoleViaHttp() drives the ACL endpoints as the superuser.
 *
 * @param  array<int, array{entity_id: int, entity_type: string, affiliation_type: string}>  $affiliations
 */
function affiliateTestUserWithEntities(array $affiliations): void
{
    createRoleViaHttp(
        roleName: 'affiliation-primitives',
        affiliations: $affiliations,
        member: test()->test_user,
        permissions: [test()->permission],
    );

    // The permission object is cached per user; the fresh role has to be visible to the resolver.
    cache()->flush();
    test()->test_user = test()->test_user->refresh();
    test()->actingAs(test()->test_user);
}
