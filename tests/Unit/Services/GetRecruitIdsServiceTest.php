<?php

use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Web\Services\GetRecruitIdsService;

it('returns recruit ids and caches values', function () {
    assignPermissionToTestUser('superuser');

    Application::factory()->count(5)->create([
        'applicationable_type' => \Seatplus\Auth\Models\User::class,
        'applicationable_id' => \Seatplus\Auth\Models\User::factory(),
    ]);

    cache()->flush();

    expect(test()->test_user)->can('superuser')->toBeTrue()
        ->and(test()->test_user)->can('can accept or deny applications')->toBeTrue();

    test()->actingAs(test()->test_user);

    $character_id = test()->test_character->character_id;

    $cache_key = hash('sha256', implode(', ', [$character_id]));

    $recruit_ids = GetRecruitIdsService::get();

    expect($recruit_ids)->toHaveCount(5)
        ->and(cache($cache_key))->toBe($recruit_ids);
});

it('returns recruit ids for directors', function () {
    Application::factory()->count(5)->create([
        'applicationable_type' => \Seatplus\Auth\Models\User::class,
        'applicationable_id' => \Seatplus\Auth\Models\User::factory(),
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
