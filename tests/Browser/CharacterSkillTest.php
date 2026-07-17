<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Skills\Skill;
use Seatplus\Eveapi\Models\Skills\SkillQueue;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Type;

if (! function_exists('deviceVisit')) {
    /**
     * Visit $url on the given viewport ("desktop" or "iphone"). Browser tests run in the core app,
     * whose tests/Pest.php is not overlaid, so this helper is defined here (guarded) alongside the
     * suite's other function_exists helpers rather than in tests/Pest.php.
     */
    function deviceVisit(string $device, string $url, array $options = []): mixed
    {
        // iPhone: build a persistent page at the mobile viewport the same way visit() builds the
        // desktop one, so the page loads mobile from the start (no desktop-load-then-resize reflow,
        // no per-call re-navigation like ->on()->iPhone15()).
        if ($device === 'iphone') {
            return new PendingAwaitablePage(
                Playwright::defaultBrowserType(),
                Device::IPHONE_15,
                $url,
                $options,
            );
        }

        return visit($url, $options);
    }
}

if (! function_exists('snap')) {
    /**
     * Settle before screenshotting: flip lazy EVE-image portraits/logos to eager so off-screen
     * (full-page) images fetch, wait for the network to go idle, then capture — so screenshots show
     * resolved images instead of loading placeholders. Best-effort: a slow/absent image won't fail.
     */
    function snap($page, string $name): void
    {
        $page->script("document.querySelectorAll('img').forEach((i) => { i.loading = 'eager'; });");
        $page->waitForEvent('networkidle');
        $page->screenshot(true, $name);
    }
}

if (! function_exists('realCharacterId')) {
    /**
     * A real EVE character id (verified CEO) so images.evetech.net serves a real portrait in
     * screenshots instead of the generic default it returns for fabricated ids. Picks one not yet
     * used in this (RefreshDatabase-isolated) test; falls back to a random id if the pool is spent.
     */
    function realCharacterId(): int
    {
        $pool = [197343093, 1319140135, 92081232, 1191750472, 94391213, 887625289, 1435633555, 1809892636];
        $available = array_values(array_diff($pool, CharacterInfo::query()->pluck('character_id')->all()));

        return $available[0] ?? fake()->unique()->numberBetween(9000000, 98000000);
    }
}

if (! function_exists('attachOwnedCharacter')) {
    /**
     * Attach an additional owned character to the same user that owns $existing, so a browser test
     * can exercise the multi-character case — the skills view renders one section per character the
     * logged-in user owns, not just the main. Returns the new character. Guarded so each Browser
     * file can define it standalone without colliding when the suite loads several.
     */
    function attachOwnedCharacter(CharacterInfo $existing): CharacterInfo
    {
        $user = CharacterUser::query()
            ->where('character_id', $existing->character_id)
            ->firstOrFail()
            ->user;

        $character = CharacterInfo::factory()->create(['character_id' => realCharacterId()]);

        CharacterUser::create([
            'user_id' => $user->getKey(),
            'character_id' => $character->character_id,
            'character_owner_hash' => sha1((string) $character->character_id),
        ]);

        return $character;
    }
}

if (! function_exists('makeSkillType')) {
    /**
     * Ensure a real published skill Type (backed by a real Group in the "Skill" category, id 16)
     * exists and return it. Skills render their type.name and group.name (the card header), so real
     * EVE ids/names make the screenshot read like an in-game skill sheet. Uses the factories (which
     * bypass mass-assignment guarding) + existence checks so a type/group/category is created once
     * and shared across skills — never firstOrCreate on the guarded universe models.
     *
     * @param  array{type: int, type_name: string, group: int, group_name: string}  $skill
     */
    function makeSkillType(array $skill): Type
    {
        // Category 16 = "Skill" for every trainable skill in EVE.
        if (! Category::query()->whereKey(16)->exists()) {
            Category::factory()->create(['category_id' => 16, 'name' => 'Skill', 'published' => true]);
        }
        if (! Group::query()->whereKey($skill['group'])->exists()) {
            Group::factory()->create(['group_id' => $skill['group'], 'category_id' => 16, 'name' => $skill['group_name'], 'published' => true]);
        }

        return Type::query()->whereKey($skill['type'])->first()
            ?? Type::factory()->create(['type_id' => $skill['type'], 'group_id' => $skill['group'], 'name' => $skill['type_name'], 'published' => true]);
    }
}

if (! function_exists('seedCharacterSkills')) {
    /**
     * Seed a small, deterministic skill sheet for $character: two trained skills in the "Gunnery"
     * group and one in "Spaceship Command", plus one skill actively training in the queue (a future
     * finish_date so it survives the controller's `finish_date >= now()` filter). Returns the type
     * name of the queued skill so the caller can assert it renders in the Skill Queue card.
     */
    function seedCharacterSkills(CharacterInfo $character): string
    {
        $gunnery = makeSkillType(['type' => 3300, 'type_name' => 'Gunnery', 'group' => 255, 'group_name' => 'Gunnery']);
        $smallHybrid = makeSkillType(['type' => 3301, 'type_name' => 'Small Hybrid Turret', 'group' => 255, 'group_name' => 'Gunnery']);
        $spaceshipCommand = makeSkillType(['type' => 3327, 'type_name' => 'Spaceship Command', 'group' => 257, 'group_name' => 'Spaceship Command']);

        foreach ([$gunnery, $smallHybrid, $spaceshipCommand] as $type) {
            Skill::factory()->create([
                'character_id' => $character->character_id,
                'skill_id' => $type->type_id,
                'active_skill_level' => 5,
                'trained_skill_level' => 5,
                'skillpoints_in_skill' => 256_000,
            ]);
        }

        // One skill actively training — future finish_date so the skillQueue endpoint returns it.
        $queued = makeSkillType(['type' => 3302, 'type_name' => 'Small Projectile Turret', 'group' => 255, 'group_name' => 'Gunnery']);
        SkillQueue::factory()->create([
            'character_id' => $character->character_id,
            'skill_id' => $queued->type_id,
            'queue_position' => 0,
            'finished_level' => 5,
            'start_date' => now()->subDay()->format('Y-m-d H:i:s'),
            'finish_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
        ]);

        return $queued->name;
    }
}

/*
 * Character skills browser tests — run against the real assembled core app.
 *
 * Covers the axios/Ziggy-free Skills view: the page renders one SkillsComponent per owned
 * character (getCharacterIds), and each SkillQueue/Skills child fetches its rows on mount via
 * getJson() + Wayfinder actions (get.character.skills / get.character.skill.queue → ->get()).
 * Skills group by type.group.name into CardWithHeader cards; the queue lists items with a future
 * finish_date. A user always sees their OWN characters' skills, so no permission is granted.
 * Provisioning comes from the suite helper actingAsCharacter() (core tests/Pest.php).
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
