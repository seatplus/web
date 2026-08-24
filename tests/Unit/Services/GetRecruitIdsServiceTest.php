<?php

use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Services\GetRecruitIdsService;

beforeEach(function () {
    // createRoleViaHttp() defaults its actor to test()->superuser, which TestCase does not seed.
    test()->superuser = Event::fakeFor(function () {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::findOrCreate('superuser'));

        return $user;
    });
});

it('returns recruit ids and caches values', function () {
    assignPermissionToTestUser('superuser');

    Application::factory()->count(5)->create([
        'applicationable_type' => User::class,
        'applicationable_id' => User::factory(),
    ]);

    cache()->flush();

    expect(test()->test_user->can('superuser'))->toBeTrue()
        ->and(test()->test_user->can('can accept or deny applications'))->toBeTrue();

    test()->actingAs(test()->test_user);

    $recruit_ids = GetRecruitIdsService::get();

    expect($recruit_ids)->toHaveCount(5);

    // The entry is keyed on the *inputs* of the recruiter scope (roles, corporation roles, their
    // affiliation rows, superuser) rather than on a resolved affiliated id array — so rather than
    // recomputing the key here, prove the caching by dropping the applications the value came from.
    Application::query()->delete();

    expect(GetRecruitIdsService::get())->toBe($recruit_ids);
});

it('returns recruit ids for directors', function () {
    Application::factory()->count(5)->create([
        'applicationable_type' => User::class,
        'applicationable_id' => User::factory(),
        'corporation_id' => test()->test_character->corporation->corporation_id,
    ]);

    CharacterRole::query()->cursor()->each(fn ($role) => $role->delete());

    $character_role = CharacterRole::factory()->create([
        'roles' => ['Director'],
        'character_id' => test()->test_character->character_id,
    ]);

    expect($character_role->hasRole('roles', 'Director'))->toBeTrue();

    expect(test()->test_user->can('superuser'))->toBeFalse();

    test()->actingAs(test()->test_user);

    expect(GetRecruitIdsService::get())
        ->toHaveCount(5);
});

it('scopes recruits to the corporations the role is affiliated with', function () {
    // The scope arm a Director never exercises: reach granted by a role's affiliation rather than by an
    // in-game corporation role. Resolved as an AffiliationResolver subquery on `corporation_id`, so the
    // affiliated corporation set is never pulled into PHP.
    $recruited_for = CorporationInfo::factory()->create();
    $someone_else = CorporationInfo::factory()->create();

    $mine = openApplicationsTo($recruited_for->corporation_id, 3);
    openApplicationsTo($someone_else->corporation_id, 2);

    affiliateTestUserWith($recruited_for->corporation_id);

    test()->actingAs(test()->test_user->refresh());

    expect(GetRecruitIdsService::get())
        ->toHaveCount(3)
        ->and(array_values(GetRecruitIdsService::get()))
        ->toEqualCanonicalizing($mine);
});

it('re-keys the cache when the role affiliations change', function () {
    // The reason the key covers the role's affiliation rows and not just the cached role-id slices: an
    // admin widening a role's affiliations moves the resolved set while the role ids stay put. Without
    // the affiliation rows in the key basis the stale entry would stand for the whole 15-minute TTL.
    $recruited_for = CorporationInfo::factory()->create();
    $sister_corporation = CorporationInfo::factory()->create();

    $mine = openApplicationsTo($recruited_for->corporation_id, 1);
    $sisters = openApplicationsTo($sister_corporation->corporation_id, 1);

    $role = affiliateTestUserWith($recruited_for->corporation_id);

    test()->actingAs(test()->test_user->refresh());

    expect(array_values(GetRecruitIdsService::get()))->toEqualCanonicalizing($mine);

    // Widen the role to the sister corporation — deliberately without flushing the cache.
    test()->actingAs(test()->superuser)
        ->postJson(route('acl.update.manual', $role->id), [
            'affiliated' => array_map(fn (int $corporation_id): array => [
                'entity_id' => $corporation_id,
                'entity_type' => 'corporation',
                'affiliation_type' => 'allowed',
            ], [$recruited_for->corporation_id, $sister_corporation->corporation_id]),
        ])
        ->assertRedirect();

    test()->actingAs(test()->test_user->refresh());

    expect(array_values(GetRecruitIdsService::get()))
        ->toEqualCanonicalizing([...$mine, ...$sisters]);
});

it('does not serve a superuser recruit list to a user with the same empty scope', function () {
    // The superuser bypass now lives inside constrainToAffiliated() rather than in a when(! superuser)
    // wrapper here, so it has to be part of the cache key: without it these two users — neither holding
    // a role nor an in-game corporation role — would hash to the same key and share an entry.
    Application::factory()->count(5)->create([
        'applicationable_type' => User::class,
        'applicationable_id' => User::factory(),
    ]);

    assignPermissionToTestUser('superuser');

    $nobody = Event::fakeFor(fn () => User::factory()->create());
    CharacterRole::query()
        ->where('character_id', $nobody->characters->first()->character_id)
        ->update(['roles' => []]);

    cache()->flush();

    test()->actingAs(test()->test_user);
    expect(GetRecruitIdsService::get())->toHaveCount(5);

    test()->actingAs($nobody);
    expect(GetRecruitIdsService::get())->toBe([]);
});

/**
 * Open applications to $corporation_id, one applicant user (and therefore one character) each.
 *
 * @return array<int, int> the applicants' character ids — what GetRecruitIdsService returns
 */
function openApplicationsTo(int $corporation_id, int $count): array
{
    return collect(Application::factory()->count($count)->create([
        'applicationable_type' => User::class,
        'applicationable_id' => User::factory(),
        'corporation_id' => $corporation_id,
        'status' => 'open',
    ]))
        ->flatMap(function (Application $application) {
            $applicant = $application->applicationable;

            return $applicant instanceof User ? $applicant->characters->pluck('character_id') : collect();
        })
        ->map(fn (int|string $character_id) => intval($character_id))
        ->all();
}

/**
 * Give the test user a role that grants the recruiter permission, affiliated to $corporation_id.
 */
function affiliateTestUserWith(int $corporation_id): Role
{
    $role = createRoleViaHttp(
        roleName: 'recruiter',
        affiliations: [
            [
                'entity_id' => $corporation_id,
                'entity_type' => 'corporation',
                'affiliation_type' => 'allowed',
            ],
        ],
        member: test()->test_user,
        permissions: ['can accept or deny applications'],
    );

    cache()->flush();

    return $role;
}
