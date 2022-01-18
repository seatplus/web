<?php

use Illuminate\Support\Facades\Cache;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Services\GetRecruitIdsService;

beforeEach(fn () => assignPermissionToTestUser('superuser'));

it('returns recruit ids and caches values', function () {

    \Seatplus\Eveapi\Models\Application::factory()->count(5)->create([
        'applicationable_type' => \Seatplus\Auth\Models\User::class,
        'applicationable_id' => \Seatplus\Auth\Models\User::factory(),
    ]);

    expect(test()->test_user)->can('superuser')->toBeTrue();

    test()->actingAs(test()->test_user);

    $cache_key = sprintf('can accept or deny applications:%s', test()->test_user->id);

    $recruit_ids = GetRecruitIdsService::get();

    expect($recruit_ids)->toHaveCount(5);
    expect(cache($cache_key))->toBe($recruit_ids);

});
