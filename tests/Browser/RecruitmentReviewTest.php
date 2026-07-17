<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\BatchUpdate;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Recruitment\ApplicationLogs;

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

/*
 * Recruitment character-review browser tests — run against the real assembled core app.
 *
 * Covers the (axios/Ziggy-free) review page at /corporation/recruitment/application/{id}:
 *   - LogTab.vue: the default "Log" tab renders the application activity timeline and the
 *     "Leave a comment" form (the comment PUT now targets a Wayfinder action url()).
 *   - UpdateCharacterComponent.vue: the per-character "… last updated" block + "Update" button;
 *     the update action now fetches/dispatches via Functions/http (getJson/post) + Wayfinder
 *     instead of raw axios + Ziggy route().
 *
 * The acting user is made a superuser so it bypasses both the `can accept or deny applications`
 * route permission and the CheckAffiliationForApplication middleware — a browser test only needs
 * to reach and render the page, not exercise the affiliation resolution (covered by feature tests).
 */

uses(RefreshDatabase::class);

if (! function_exists('actingAsRecruiter')) {
    /**
     * Turn the logged-in character's user into a superuser recruiter, so it may open any
     * application review page. Mirrors the superuser setup used by the recruitment feature tests.
     */
    function actingAsRecruiter(CharacterInfo $character): User
    {
        $user = User::whereMainCharacterId($character->character_id)->sole();
        $user->givePermissionTo(Permission::findOrCreate('superuser'));

        return $user;
    }
}

if (! function_exists('makeRecruitApplication')) {
    /**
     * Build an open, User-type application under review: a recruit User owning a single real
     * character (with a finished BatchUpdate so the "Update" action is eligible) plus one comment
     * log entry authored by $reviewer. Returns [$application, $recruitCharacter].
     *
     * @return array{0: Application, 1: CharacterInfo}
     */
    function makeRecruitApplication(User $reviewer): array
    {
        // Recruit account: one owned character, wired up the same way actingAsCharacter() builds
        // the logged-in user (main_character_id + CharacterUser pivot) so recruit.main_character and
        // recruit.characters both resolve in Pages/Corporation/Recruitment/Application.vue.
        $recruitCharacter = CharacterInfo::factory()->create(['character_id' => realCharacterId()]);

        $recruit = new User;
        $recruit->main_character_id = $recruitCharacter->character_id;
        $recruit->save();

        CharacterUser::create([
            'user_id' => $recruit->getKey(),
            'character_id' => $recruitCharacter->character_id,
            'character_owner_hash' => sha1((string) $recruitCharacter->character_id),
        ]);

        // Finished > 1h ago → UpdateCharacterComponent.canUpdate is true, so both the last-updated
        // timestamp and the "Update" button render.
        BatchUpdate::create([
            'batchable_id' => $recruitCharacter->character_id,
            'batchable_type' => CharacterInfo::class,
            'started_at' => now()->subHours(3),
            'finished_at' => now()->subHours(2),
        ]);

        $application = Application::factory()->create([
            'corporation_id' => CorporationInfo::factory()->create()->corporation_id,
            'applicationable_type' => User::class,
            'applicationable_id' => $recruit->getKey(),
            'status' => 'open',
        ]);

        // One reviewer comment so the activity timeline renders a comment entry (its causer must
        // resolve a main_character, which the reviewer superuser has).
        ApplicationLogs::create([
            'application_id' => $application->id,
            'causer_id' => $reviewer->getKey(),
            'causer_type' => User::class,
            'type' => 'comment',
            'comment' => 'Looks promising, reviewing the assets next.',
        ]);

        return [$application, $recruitCharacter];
    }
}

it('renders the application activity log on the default Log tab', function (string $device) {
    $recruiterCharacter = actingAsCharacter();
    $reviewer = actingAsRecruiter($recruiterCharacter);

    [$application, $recruitCharacter] = makeRecruitApplication($reviewer);

    $page = deviceVisit($device, "/corporation/recruitment/application/{$application->id}");
    $page->assertNoSmoke();

    // Log is the default active tab: the timeline header, the synthetic "has applied" decision
    // entry, and the comment form are all present without switching tabs.
    $page->waitForText('Activity Log');
    $page->waitForText('has applied');
    $page->waitForText('Looks promising, reviewing the assets next.');
    $page->assertSee('Leave a comment');
    $page->assertSee($recruitCharacter->name);

    snap($page, "recruitment-review-log-tab-{$device}");
})->with(['desktop', 'iphone']);

it('renders the per-character update action and reflects an in-flight update', function (string $device) {
    $recruiterCharacter = actingAsCharacter();
    $reviewer = actingAsRecruiter($recruiterCharacter);

    [$application, $recruitCharacter] = makeRecruitApplication($reviewer);

    $page = deviceVisit($device, "/corporation/recruitment/application/{$application->id}");
    $page->assertNoSmoke();

    // UpdateCharacterComponent renders "<name> last updated" and, since the last BatchUpdate
    // finished > 1h ago, an "Update" button.
    $page->waitForText("{$recruitCharacter->name} last updated");
    $page->assertSee('Update');

    snap($page, "recruitment-review-update-character-{$device}");

    // Clicking Update flips the component into its in-flight state ("updating" + spinner). This
    // exercises the migrated post()/Wayfinder dispatch path (formerly axios.post + Ziggy route()).
    $page->click('Update');
    $page->waitForText('updating');

    snap($page, "recruitment-review-update-character-updating-{$device}");
})->with(['desktop', 'iphone']);
