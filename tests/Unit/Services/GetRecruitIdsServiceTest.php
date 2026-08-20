<?php

use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Web\Services\GetAffiliatedIds;
use Seatplus\Web\Services\GetRecruitIdsService;

it('returns recruit ids and caches values', function () {
    assignPermissionToTestUser('superuser');

    Application::factory()->count(5)->create([
        'applicationable_type' => User::class,
        'applicationable_id' => User::factory(),
    ]);

    cache()->flush();

    expect(test()->test_user)->can('superuser')->toBeTrue()
        ->and(test()->test_user)->can('can accept or deny applications')->toBeTrue();

    test()->actingAs(test()->test_user);

    // The cache key is derived from the affiliation *inputs* (user, permission/role, their cached
    // role-id slices) — not from the resolved affiliated set, which used to be materialised just
    // to hash it.
    $cache_key = 'recruit_ids:'.(new GetAffiliatedIds(test()->test_user))
        ->affiliationCacheKey('can accept or deny applications', 'Director');

    $recruit_ids = GetRecruitIdsService::get();

    expect($recruit_ids)->toHaveCount(5)
        ->and(cache($cache_key))->toBe($recruit_ids);
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
