<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Assets\Asset;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Eveapi\Models\Recruitment\ApplicationLogs;
use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Eveapi\Models\Skills\Skill;
use Seatplus\Eveapi\Models\Skills\SkillQueue;
use Seatplus\Eveapi\Models\SsoScopes;
use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Location;
use Seatplus\Eveapi\Models\Universe\Station;
use Seatplus\Eveapi\Models\Universe\Type;
use Seatplus\Web\Models\Onboarding;
use Seatplus\Web\Models\Recruitment\Enlistment;
use Seatplus\Web\Models\Recruitment\EnlistmentReviewRound;

/*
|--------------------------------------------------------------------------
| Browser suite helpers
|--------------------------------------------------------------------------
|
| Every browser test file `require_once`s this one shared file. The browser
| suite runs against the fully-assembled core app (not web's Testbench
| harness), and Pest only auto-loads the ROOT tests/Pest.php — a Pest.php
| synced into tests/Browser/web is never loaded. So rather than re-declaring
| each helper per file under function_exists guards, they live here once and
| travel with the tests when the package's tests/Browser is synced into core.
|
| require_once is idempotent, so a single definition is safe even when the
| suite loads many test files in one run.
|
*/

/* ---------------------------------------------------------------- viewport + capture */

/**
 * Visit $url on the given viewport ("desktop" or "iphone").
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

/* -------------------------------------------------------------------- provisioning */

// actingAsCharacter / giveCorporationRole historically lived in core's ROOT tests/Pest.php (the one
// file Pest auto-loads). While that definition is being retired in favour of this shared file, both
// can be present at once — e.g. a web PR's browser job runs these tests against a core checkout whose
// Pest.php still declares them. Guard only these two so they coexist without a redeclare fatal; the
// rest of the helpers below never existed in Pest.php and need no guard.
if (! function_exists('actingAsCharacter')) {
    /**
     * Provision a logged-in user owning a single controlled character and return it.
     *
     * We create ONE CharacterInfo (its factory builds the affiliation/refresh-token/role)
     * linked as the main character rather than User::factory(), whose two-character graph
     * has intermittently-colliding CharacterAffiliation ids and renders pages blank. Queue
     * is faked so character-creation model events don't dispatch real ESI jobs.
     */
    function actingAsCharacter(): CharacterInfo
    {
        Queue::fake();

        // Use a real EVE character id so images.evetech.net serves an actual portrait in browser-test
        // screenshots instead of the generic default it returns for fabricated ids. Pick one from the
        // pool (real GSF members) not already taken in this RefreshDatabase-isolated test — so repeated
        // logins/characters vary — and fall back to the factory's random id if the pool is exhausted.
        $realCharacterIds = Arr::shuffle([240070320, 197343093, 1319140135, 92081232, 94391213, 887625289, 1435633555, 1809892636]);
        $availableCharacterIds = array_values(array_diff(
            $realCharacterIds,
            CharacterInfo::query()->pluck('character_id')->all()
        ));

        $character = CharacterInfo::factory()
            ->create($availableCharacterIds ? ['character_id' => $availableCharacterIds[0]] : []);

        $user = new User;
        $user->main_character_id = $character->character_id;
        $user->save();

        CharacterUser::create([
            'user_id' => $user->getKey(),
            'character_id' => $character->character_id,
            'character_owner_hash' => sha1((string) $character->character_id),
        ]);

        Onboarding::create(['user_id' => $user->getKey()]);

        test()->actingAs($user);

        return $character;
    }
}

if (! function_exists('giveCorporationRole')) {
    /**
     * Give a character an in-game corporation role (Director by default), which grants the
     * owning user corp-scoped access. CharacterInfo::factory() already creates an empty
     * CharacterRole, so update it in place.
     */
    function giveCorporationRole(CharacterInfo $character, string $role = 'Director'): void
    {
        CharacterRole::updateOrCreate(
            ['character_id' => $character->character_id],
            ['roles' => [$role]],
        );
    }
}

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

/**
 * Attach an additional owned character to the same user that owns $existing, so a browser test
 * can exercise the multi-character case — character-scoped pages aggregate over / render per every
 * character the logged-in user owns, not just the main. Returns the newly attached character.
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

/**
 * The user account that owns $characterId.
 */
function userOfCharacter(int $characterId): User
{
    return CharacterUser::query()->where('character_id', $characterId)->firstOrFail()->user;
}

/**
 * Point $refreshToken at a freshly-minted token carrying $scopes.
 *
 * @param  array<int, string>  $scopes
 */
function updateRefreshTokenWithScopes(RefreshToken $refreshToken, array $scopes): RefreshToken
{
    $helper_token = RefreshToken::factory()->scopes($scopes)->make([
        'character_id' => $refreshToken->character_id,
    ]);

    $refreshToken->token = $helper_token->token;
    $refreshToken->save();

    return $refreshToken;
}

/* ----------------------------------------------------------------- slide-over assertions */

/**
 * Both slide-overs teleport into <div id="destination">, outside the page's own content. The
 * pages also render a card per character/corporation with the same name, so page-wide assertSee
 * can't tell the panel apart — this scopes an assertion to #destination.
 */
function slideOverSees(string $text): string
{
    return "(document.querySelector('#destination')?.innerText ?? '').includes(".json_encode($text).')';
}

/* ------------------------------------------------------------------- access-control groups */

/**
 * Grant the character's user the "view access control" permission.
 */
function grantAclView(int $characterId): User
{
    $user = userOfCharacter($characterId);
    $user->givePermissionTo(Permission::findOrCreate('view access control'));

    return $user;
}

/**
 * Grant the character's user the "administrate access control groups" permission.
 */
function grantAclAdmin(int $characterId): User
{
    $user = userOfCharacter($characterId);
    $user->givePermissionTo(Permission::findOrCreate('administrate access control groups'));

    return $user;
}

function makeManualRole(string $name): Role
{
    $role = Role::findById(Role::create(['name' => $name])->id);
    $role->update(['type' => RoleType::MANUAL]);

    return $role;
}

function makeOnRequestRole(string $name): Role
{
    $role = Role::findById(Role::create(['name' => $name])->id);
    $role->update(['type' => RoleType::ON_REQUEST]);

    return $role;
}

function makeOptInRoleForCorporation(string $name, int $corporationId): Role
{
    $role = Role::findById(Role::create(['name' => $name])->id);
    $role->update(['type' => RoleType::OPT_IN]);

    RoleMembership::create([
        'role_id' => $role->id,
        'entity_type' => CorporationInfo::class,
        'entity_id' => $corporationId,
    ]);

    return $role;
}

/** Give $user an active membership of $role (optionally as a moderator). */
function addRoleMember(Role $role, User $user, bool $moderator = false): void
{
    RoleMembership::create([
        'role_id' => $role->id,
        'entity_type' => User::class,
        'entity_id' => $user->getKey(),
        'status' => 'active',
        'can_moderate' => $moderator,
    ]);
}

/** Seed a distinct user (named character) with a pending application to $role. */
function makeApplicant(Role $role, string $name): CharacterInfo
{
    $character = CharacterInfo::factory()->create(['name' => $name]);

    $user = new User;
    $user->main_character_id = $character->character_id;
    $user->save();

    CharacterUser::create([
        'user_id' => $user->getKey(),
        'character_id' => $character->character_id,
        'character_owner_hash' => sha1((string) $character->character_id),
    ]);

    RoleMembership::create([
        'role_id' => $role->id,
        'entity_type' => User::class,
        'entity_id' => $user->getKey(),
        'status' => 'pending',
    ]);

    return $character;
}

/* -------------------------------------------------------------------------- observation */

/** Grant the acting user observation access to their corporation (permission + director role). */
function makeObserverOfCorporation(CharacterInfo $character): User
{
    $user = userOfCharacter($character->character_id);
    $user->givePermissionTo(Permission::findOrCreate('view member compliance'));

    CharacterRole::updateOrCreate(
        ['character_id' => $character->character_id],
        ['roles' => ['Director']],
    );

    return $user;
}

/** Configure the corporation with SSO scopes (so it is observable) and member-tracking activity. */
function seedObservableCorporation(CharacterInfo $character): void
{
    SsoScopes::updateOrCreate(
        ['morphable_id' => $character->corporation_id],
        [
            'morphable_type' => CorporationInfo::class,
            'type' => 'default',
            'selected_scopes' => ['esi-assets.read_assets.v1'],
        ],
    );

    CorporationMemberTracking::updateOrCreate(
        ['corporation_id' => $character->corporation_id, 'character_id' => $character->character_id],
        ['logon_date' => now()->subDay(), 'start_date' => now()->subMonths(3)],
    );
}

/* -------------------------------------------------------------------------- recruitment */

function makeControlGroup(string $name): Role
{
    return Role::findById(Role::create(['name' => $name])->id);
}

function addGroupMember(Role $role, User $user): void
{
    RoleMembership::create([
        'role_id' => $role->id,
        'entity_type' => User::class,
        'entity_id' => $user->getKey(),
        'status' => 'active',
    ]);
    $user->assignRole($role);
}

/** Grant the acting user recruiter access to their corporation (permission + director role). */
function makeRecruiterOfCorporation(CharacterInfo $character, string $permission): User
{
    $user = userOfCharacter($character->character_id);
    $user->givePermissionTo(Permission::findOrCreate($permission));

    CharacterRole::updateOrCreate(
        ['character_id' => $character->character_id],
        ['roles' => ['Director']],
    );

    return $user;
}

/**
 * @param  array<int, array{label: string, role: ?Role}>  $stages
 */
function openPostingWithStages(int $corporationId, array $stages): void
{
    Enlistment::query()->updateOrCreate(['corporation_id' => $corporationId], ['type' => 'user']);

    foreach ($stages as $position => $stage) {
        EnlistmentReviewRound::query()->updateOrCreate(
            ['corporation_id' => $corporationId, 'position' => $position],
            ['label' => $stage['label'], 'role_id' => $stage['role']?->id],
        );
    }
}

/** Create an applicant with an open application to $corporationId, already advanced to $stage. */
function seedApplicantAtStage(int $corporationId, string $name, int $stage = 0): Application
{
    $character = CharacterInfo::factory()->create(['character_id' => realCharacterId(), 'name' => $name]);

    $user = new User;
    $user->main_character_id = $character->character_id;
    $user->save();

    CharacterUser::create([
        'user_id' => $user->getKey(),
        'character_id' => $character->character_id,
        'character_owner_hash' => sha1((string) $character->character_id),
    ]);

    $application = Application::query()->create([
        'corporation_id' => $corporationId,
        'applicationable_type' => User::class,
        'applicationable_id' => $user->getKey(),
    ]);

    // Each recorded decision advances the application one stage.
    for ($i = 0; $i < $stage; $i++) {
        ApplicationLogs::query()->create([
            'application_id' => $application->id,
            'causer_type' => User::class,
            'causer_id' => $user->getKey(),
            'type' => 'decision',
            'comment' => '',
        ]);
    }

    return $application;
}

/* ---------------------------------------------------------------------- domain seeders */

/**
 * Give $owner one character-type contact and return the contacted character. The contact_id is a
 * real EVE character (its own factory CharacterInfo + CharacterAffiliation), so the row's
 * EntityByIdBlock resolves the name/portrait offline via resolve.id. The contact is attached to
 * the owner (contactable), which is what the index() `has('contacts')` filter keys off of.
 */
function makeCharacterContact(CharacterInfo $owner): CharacterInfo
{
    $contactCharacter = CharacterInfo::factory()->create(['character_id' => realCharacterId()]);

    Contact::factory()->create([
        'contactable_id' => $owner->character_id,
        'contactable_type' => CharacterInfo::class,
        'contact_id' => $contactCharacter->character_id,
        'contact_type' => 'character',
        'standing' => 10.0,
    ]);

    return $contactCharacter;
}

/**
 * Create $count contracts owned by (attached to) $character and return them. Defaults
 * model a personal, unaccepted (outstanding) contract issued to and assigned to the
 * character itself, so every id the row renders (issuer/assignee) resolves offline
 * from the DB. Override e.g. acceptor_id/status for the accepted case.
 *
 * @param  array<string, mixed>  $overrides
 * @return Collection<int, Contract>
 */
function makeCharacterContracts(CharacterInfo $character, int $count, array $overrides = []): Collection
{
    // Share supporting records across the whole batch. Left to its defaults the Contract
    // factory spins up a fresh Location→Station→System and a CorporationInfo per contract,
    // and those random universe/corp ids collide (e.g. universe_systems_pkey) once enough
    // rows are created. Create them once and reuse the ids.
    $contracts = Contract::factory()->count($count)->create(array_merge([
        'issuer_id' => $character->character_id,
        'assignee_id' => $character->character_id,
        'for_corporation' => false,
        'acceptor_id' => 0,
        'status' => 'outstanding',
        'issuer_corporation_id' => CorporationInfo::factory()->create()->corporation_id,
        'start_location_id' => Location::factory()->withStation()->create()->location_id,
        'end_location_id' => Location::factory()->withStation()->create()->location_id,
    ], $overrides));

    $character->contracts()->syncWithoutDetaching($contracts->pluck('contract_id')->all());

    return $contracts;
}

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

    // One skill actively training — future finish_date so the skillQueue deferred prop returns it.
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

/**
 * Create one named asset owned by $character at $locationId (rooted at $rootLocationId).
 * It gets a real published Type backed by a real Group so the ItemDetails page — whose
 * LocationSlot dereferences asset.type.name / asset.type.group.name without a null-guard —
 * renders cleanly.
 *
 * @param  array<string, mixed>  $overrides
 */
function makeCharacterAsset(CharacterInfo $character, int $locationId, int $rootLocationId, array $overrides = []): Asset
{
    // Real EVE type/group/category so images.evetech.net serves a real item image (ships →
    // 'render', minerals → 'icon') instead of the generic default it returns for fabricated ids.
    // Shared via firstOrCreate so many assets can reuse a type (as real inventories do).
    $realType = fake()->randomElement([
        ['type' => 587, 'type_name' => 'Rifter', 'group' => 25, 'group_name' => 'Frigate', 'category' => 6, 'category_name' => 'Ship'],
        ['type' => 24698, 'type_name' => 'Drake', 'group' => 419, 'group_name' => 'Combat Battlecruiser', 'category' => 6, 'category_name' => 'Ship'],
        ['type' => 638, 'type_name' => 'Raven', 'group' => 27, 'group_name' => 'Battleship', 'category' => 6, 'category_name' => 'Ship'],
        ['type' => 34, 'type_name' => 'Tritanium', 'group' => 18, 'group_name' => 'Mineral', 'category' => 4, 'category_name' => 'Material'],
    ]);

    // Use the factories (they bypass mass-assignment guarding) + existence checks so a real
    // type/group/category is created once and shared across assets.
    if (! Category::query()->whereKey($realType['category'])->exists()) {
        Category::factory()->create(['category_id' => $realType['category'], 'name' => $realType['category_name'], 'published' => true]);
    }
    if (! Group::query()->whereKey($realType['group'])->exists()) {
        Group::factory()->create(['group_id' => $realType['group'], 'category_id' => $realType['category'], 'name' => $realType['group_name'], 'published' => true]);
    }
    $type = Type::query()->whereKey($realType['type'])->first()
        ?? Type::factory()->create(['type_id' => $realType['type'], 'group_id' => $realType['group'], 'name' => $realType['type_name'], 'published' => true]);

    return Asset::factory()->withName()->create(array_merge([
        'assetable_id' => $character->character_id,
        'assetable_type' => CharacterInfo::class,
        'location_id' => $locationId,
        'root_location_id' => $rootLocationId,
        'location_flag' => 'Hangar',
        'type_id' => $type->type_id,
    ], $overrides));
}

/**
 * Seed a three-level nesting rooted in a station location:
 * capital ship → freighter → container → cargo (each child.location_id = parent.item_id).
 * Returns [location, capital, freighter, container, cargo].
 *
 * @return array<string, mixed>
 */
function makeNestedAssetChain(CharacterInfo $character): array
{
    $location = Location::factory()->for(Station::factory(), 'locatable')->create();
    $root = $location->location_id;

    $capital = makeCharacterAsset($character, $root, $root);
    $freighter = makeCharacterAsset($character, $capital->item_id, $root, ['location_flag' => 'ShipHangar']);
    $container = makeCharacterAsset($character, $freighter->item_id, $root, ['location_flag' => 'Cargo']);
    $cargo = makeCharacterAsset($character, $container->item_id, $root, ['location_flag' => 'Cargo']);

    return compact('location', 'capital', 'freighter', 'container', 'cargo');
}

/**
 * Assert a JS condition on the assets page, re-scrolling the list to the bottom on each poll so
 * per-location items — which lazy-load when their location scrolls into view — are present.
 * On mobile the stacked filter block pushes the first location below the fold, so a plain
 * waitForText never triggers the load; scrolling the list container does.
 */
function assertAssetsScript($page, string $condition): void
{
    $page->assertScript("(document.getElementById('assets-body')?.closest('.overflow-y-auto')?.scrollTo(0, 1e6), {$condition})");
}

/** Wait (with scroll) for a single asset/item name to appear on the assets list. */
function assetTextVisible($page, string $text): void
{
    assertAssetsScript($page, "document.body.innerText.includes('".addslashes($text)."')");
}
