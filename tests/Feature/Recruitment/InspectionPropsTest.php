<?php

use Inertia\DeferProp;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Skills\Skill;
use Seatplus\Web\Services\CharacterInspectionScrollProps;

// Regression: reviewing an applicant (and observing an employee) reuse CharacterInspectionScrollProps
// for the shared tabs. Skills and Contacts render from page props, so the service must build them —
// otherwise those tabs are blank even though the data is in the DB (visible on the character pages).
it('builds resolvable skills and a wired contacts prop per inspected character', function () {
    $character = CharacterInfo::factory()->create();
    $skill = Skill::factory()->create(['character_id' => $character->character_id]);

    $props = app(CharacterInspectionScrollProps::class)->build([(int) $character->character_id], request());

    expect($props)->toHaveKeys(['skills', 'skillQueue', 'contacts', 'mailHeaders'])
        ->and($props['contacts'])->toBeInstanceOf(DeferProp::class);

    // Resolving the deferred skills prop yields the character's own skills, keyed by character_id.
    $skills = ($props['skills'])();

    expect($skills->get($character->character_id))->toHaveCount(1)
        ->and($skills->get($character->character_id)->first()->skill_id)->toBe($skill->skill_id);
});
