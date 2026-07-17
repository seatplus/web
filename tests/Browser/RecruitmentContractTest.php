<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Contracts\Contract;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Universe\Location;

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

if (! function_exists('makeCharacterContracts')) {
    /**
     * Create $count contracts owned by (attached to) $character and return them. Defaults
     * model a personal, unaccepted (outstanding) contract issued to and assigned to the
     * character itself, so every id the row renders (issuer/assignee) resolves offline
     * from the DB. Guarded so each Browser file can define it standalone without colliding
     * when the suite loads several.
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
}

if (! function_exists('userOfCharacter')) {
    function userOfCharacter(int $characterId): User
    {
        return CharacterUser::query()->where('character_id', $characterId)->firstOrFail()->user;
    }
}

if (! function_exists('makeReviewableRecruit')) {
    /**
     * A recruit User to review: exactly one owned character (a real EVE id, also the main), so the
     * review page renders a single contract list. User::factory attaches a random character in its
     * afterCreating hook — swap it for the real-id one so the assertions target a known body id.
     *
     * @return array{0: User, 1: CharacterInfo}
     */
    function makeReviewableRecruit(): array
    {
        $user = User::factory()->create();

        $user->characterUsers()->delete();

        $character = CharacterInfo::factory()->create(['character_id' => realCharacterId()]);

        CharacterUser::create([
            'user_id' => $user->getKey(),
            'character_id' => $character->character_id,
            'character_owner_hash' => sha1((string) $character->character_id),
        ]);

        $user->update(['main_character_id' => $character->character_id]);

        return [$user->refresh(), $character];
    }
}

/*
 * Recruitment / member-compliance contract tab browser test — run against the real assembled
 * core app.
 *
 * Exercises the shared recruitment ContractTab (Pages/Corporation/Recruitment/Tabs/ContractTab.vue,
 * used by both the recruitment application page and the member-compliance review-user page) after
 * its migration off the legacy axios InfiniteLoadingHelper. The tab now renders a native Inertia
 * <InfiniteScroll> over a per-character contracts_<id> page scroll prop emitted by
 * MemberComplianceController::reviewUser — same mechanism as the character contracts page.
 *
 * Reached via the review-user page because superuser bypasses its `view member compliance` +
 * `member compliance: review user` gates (CanUserService), keeping provisioning minimal.
 * Provisioning helpers otherwise come from the suite (actingAsCharacter(), core tests/Pest.php).
 */

uses(RefreshDatabase::class);

it('merges the next recruit-contracts page in on scroll in the review contract tab', function (string $device) {
    // Reviewer: any logged-in user, elevated to superuser so the compliance gates pass.
    $reviewer = actingAsCharacter();
    userOfCharacter($reviewer->character_id)->givePermissionTo(Permission::findOrCreate('superuser'));

    // Recruit under review + a page's worth of contracts (default 15/page → several pages).
    [$recruitUser, $recruitCharacter] = makeReviewableRecruit();
    makeCharacterContracts($recruitCharacter, 40);

    // Any corporation id — reviewUser only find()s it for the target-corporation prop; with no
    // SsoScopes the review renders every recruit character (no corp-scoped character filter).
    $corporation = CorporationInfo::factory()->create();

    $url = route('corporation.review.user', [
        'corporation_id' => $corporation->corporation_id,
        'user' => $recruitUser->getKey(),
    ]);

    $page = deviceVisit($device, $url);
    $page->assertNoSmoke();

    // The header renders the recruit's main-character name via Vue — waiting on it confirms the
    // page has hydrated before we drive the (reactive) tab switch.
    $page->waitForText($recruitCharacter->name);

    // TabComponent opens on the "Log" tab; activate "Contracts". The switcher differs per viewport:
    // sm+ renders clickable <div>s; mobile (.sm:hidden) renders a native <select v-model>. The
    // <option> has no :value binding, so its value is its text ("Contracts").
    if ($device === 'iphone') {
        // Drive the mobile <select>: set its value to the "Contracts" option and fire a bubbling
        // change event so Vue's v-model updates active_element.
        $page->script("(() => { const s = document.getElementById('tabs'); s.value = 'Contracts'; s.dispatchEvent(new Event('change', { bubbles: true })); })()");
    } else {
        $page->click('Contracts');
    }

    $rows = "#contracts-body-{$recruitCharacter->character_id} > *";

    // assertScript auto-polls: waits for the tab to mount and the first scroll-prop page to render.
    // Proves the native <InfiniteScroll> list rendered on this viewport (the point of the migration).
    $page->assertScript("document.querySelectorAll('{$rows}').length > 0");

    $before = (int) $page->script("document.querySelectorAll('{$rows}').length");
    expect($before)->toBeGreaterThan(0);

    // Re-scroll the list's own container to the bottom on every poll (comma expression) so
    // InfiniteScroll's end trigger fires; passes once the next page has merged in.
    $page->assertScript("(document.getElementById('contracts-body-{$recruitCharacter->character_id}').closest('.overflow-y-auto').scrollTo(0, 1e6), document.querySelectorAll('{$rows}').length > {$before})");

    snap($page, "recruitment-contracts-infinite-scroll-{$device}");
})->with(['desktop', 'iphone']);
