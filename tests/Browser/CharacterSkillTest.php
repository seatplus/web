<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Eveapi\Models\Skills\Skill;

require_once __DIR__.'/helpers.php';

/*
 * Character skills browser tests — run against the real assembled core app.
 *
 * Covers the deferred-props Skills view: the page renders one SkillsComponent per owned
 * character (getCharacterIds), and the skills/skillQueue data arrives as Inertia deferred props
 * keyed by character_id (resolved on a follow-up partial reload after the initial render, with a
 * pulsing skeleton fallback in the meantime). Skills group by type.group.name into CardWithHeader
 * cards; the queue lists items with a future finish_date. A user always sees their OWN characters'
 * skills, so no permission is granted. Provisioning comes from the suite helper actingAsCharacter()
 * (core tests/Pest.php).
 */

uses(RefreshDatabase::class);

it('renders a character’s skills grouped into cards and its skill queue', function (string $device) {
    $character = actingAsCharacter();
    $queuedSkill = seedCharacterSkills($character);

    $page = deviceVisit($device, '/character/skills');
    $page->assertNoSmoke();
    $page->waitForText('Character Skills');

    // Skills fetch on mount and group by their type.group.name into a card per group.
    $page->waitForText('Gunnery');
    $page->waitForText('Spaceship Command');
    $page->waitForText('Small Hybrid Turret');

    // The Skill Queue card lists the actively-training skill (future finish_date).
    $page->waitForText('Skill Queue');
    $page->waitForText($queuedSkill);

    snap($page, "character-skills-{$device}");
})->with(['desktop', 'iphone']);

it('renders a skills section for every character the user owns', function (string $device) {
    $mainCharacter = actingAsCharacter();
    $secondCharacter = attachOwnedCharacter($mainCharacter);

    seedCharacterSkills($mainCharacter);
    seedCharacterSkills($secondCharacter);

    $page = deviceVisit($device, '/character/skills');
    $page->assertNoSmoke();
    $page->waitForText('Character Skills');

    // One SkillsComponent (character header + skills + queue) renders per owned character; both
    // characters' names appear in their EntityByIdBlock section headers.
    $page->waitForText($mainCharacter->name);
    $page->waitForText($secondCharacter->name);

    snap($page, "character-skills-multiple-characters-{$device}");
})->with(['desktop', 'iphone']);

it('shows an empty skill queue state when nothing is training', function (string $device) {
    $character = actingAsCharacter();

    // Trained skills but nothing in the queue → the Skill Queue card shows its empty state instead
    // of rendering nothing (the pre-refactor behaviour).
    $gunnery = makeSkillType(['type' => 3300, 'type_name' => 'Gunnery', 'group' => 255, 'group_name' => 'Gunnery']);
    Skill::factory()->create([
        'character_id' => $character->character_id,
        'skill_id' => $gunnery->type_id,
        'active_skill_level' => 5,
        'trained_skill_level' => 5,
        'skillpoints_in_skill' => 256_000,
    ]);

    $page = deviceVisit($device, '/character/skills');
    $page->assertNoSmoke();
    $page->waitForText('Character Skills');
    $page->waitForText('Skill Queue');
    $page->waitForText('No skills in training.');

    snap($page, "character-skills-empty-queue-{$device}");
})->with(['desktop', 'iphone']);
