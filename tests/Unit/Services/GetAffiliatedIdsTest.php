<?php

use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Web\Services\GetAffiliatedIds;

it('returns owned character ids when user is injected via constructor without actingAs', function () {
    $service = new GetAffiliatedIds(test()->test_user);

    $result = $service->get(permissions: ['some-permission']);

    expect($result)->toContain(test()->test_character->character_id);
});

it('returns corporation ids for users with director role without actingAs', function () {
    CharacterRole::query()->cursor()->each(fn ($role) => $role->delete());

    $corporationId = test()->test_character->corporation->corporation_id;

    CharacterRole::factory()->create([
        'roles' => ['Director'],
        'character_id' => test()->test_character->character_id,
    ]);

    // No actingAs — inject user directly
    $service = new GetAffiliatedIds(test()->test_user);

    $result = $service->get(permissions: ['some-permission'], corporationRoles: ['Director']);

    expect($result)->toContain($corporationId);
});
