<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Character\CorporationHistory;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Web\Models\Recruitment\Enlistment;

/*
 * Corporation-history browser test — runs against the real assembled core app.
 *
 * Exercises the axios/Ziggy-free CorporationHistoryComponent, which now loads the (bounded)
 * history in one shot via the native-fetch helper (Functions/http.js) + a Wayfinder URL
 * instead of the legacy InfiniteLoadingHelper. The component only renders inside the
 * recruitment review page (Pages/Corporation/Recruitment/Application.vue → TabComponent),
 * so the test provisions a single character application and opens it as a superuser — which
 * bypasses CheckAffiliationForApplication, keeping the setup to models + factories.
 */

uses(RefreshDatabase::class);

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

if (! function_exists('switchRecruitmentTab')) {
    /**
     * Activate a TabComponent tab on either viewport by driving the <select id="tabs"> that is bound
     * to `active_element` via v-model. Both layouts share that state: the mobile layout (.sm:hidden)
     * shows the select and the desktop layout (.hidden sm:block) shows a row of <div>s, but the
     * select is always mounted (merely display:none on desktop), so setting its value + dispatching a
     * bubbling change event updates active_element — and the visible desktop tab highlights — on
     * either viewport. This is deterministic; a text-click on the desktop tab instead resolves to the
     * (DOM-earlier) hidden <option> of the same label and never becomes actionable. The options carry
     * no explicit :value, so each option's value is its label text.
     */
    function switchRecruitmentTab($page, string $device, string $label): void
    {
        $value = json_encode($label);

        $page->script("const select = document.querySelector('#tabs'); select.value = {$value}; select.dispatchEvent(new Event('change', { bubbles: true }));");
    }
}

it('renders a recruit corporation history through the recruitment review page', function (string $device) {
    // The logged-in reviewer. Superuser bypasses CheckAffiliationForApplication, so no role /
    // affiliation plumbing is needed to open someone else's application.
    $reviewerCharacter = actingAsCharacter();

    $reviewer = CharacterUser::query()
        ->where('character_id', $reviewerCharacter->character_id)
        ->firstOrFail()
        ->user;

    $reviewer->givePermissionTo(Permission::findOrCreate('superuser'));

    // The recruit whose (character-type) application is being reviewed. A real EVE id renders a
    // real portrait for the corporation-history card's EntityBlock header. The applicant owns the
    // character, so link it to a user — the review page resolves the applicant's account via
    // CharacterUser (Event::fakeFor avoids the factory auto-attaching an unrelated character).
    $recruit = CharacterInfo::factory()->create(['character_id' => realCharacterId()]);
    $recruitOwner = Event::fakeFor(fn () => User::factory()->create());
    CharacterUser::factory()->create([
        'user_id' => $recruitOwner->id,
        'character_id' => $recruit->character_id,
    ]);
    $corporation = CorporationInfo::factory()->create();

    // A short, ordered corporation history. Each row's corporation_id resolves to a name offline
    // (resolve.id → CorporationInfo) so the timeline renders real corp names, not placeholders.
    $historyCorporations = CorporationInfo::factory()->count(3)->create();

    $historyCorporations->each(function (CorporationInfo $historyCorporation, int $index) use ($recruit) {
        CorporationHistory::factory()->create([
            'character_id' => $recruit->character_id,
            'corporation_id' => $historyCorporation->corporation_id,
            'record_id' => 1000 + $index,
        ]);
    });

    // A matching enlistment so WatchlistArrayAction returns a populated (object-shaped) watchlist
    // prop rather than an empty array.
    Enlistment::create([
        'corporation_id' => $corporation->corporation_id,
        'type' => 'character',
    ]);

    $application = Application::factory()->create([
        'corporation_id' => $corporation->corporation_id,
        'applicationable_type' => CharacterInfo::class,
        'applicationable_id' => $recruit->character_id,
        'status' => 'open',
    ]);

    $page = deviceVisit($device, "/corporation/recruitment/application/{$application->id}");
    $page->assertNoSmoke();
    $page->assertSee('User Application');

    // The review page opens on the "Log" tab; switch to the corporation-history tab, which mounts
    // CorporationHistoryComponent and triggers its fetch-swap load.
    switchRecruitmentTab($page, $device, 'Corporation History');

    // Corp names resolve lazily: each timeline row's ResolveIdToName only fetches once its element is
    // *fully* visible (IntersectionObserver threshold 1), so on the short iPhone viewport the history
    // card — which renders below the fold — never resolves on its own. Scroll the card's own scroll
    // container to the viewport centre, settling between, so that once the getJson load populates the
    // (short, bounded) timeline every row sits fully in view and resolves. The container renders with
    // the component, before the fetch resolves, so this targets a real element regardless of load
    // timing (the rows themselves don't exist until `results` is populated); repeating a few times
    // covers the tick where the tab has switched but the component has not yet mounted. Once resolved a
    // name stays in the DOM regardless of the final scroll position. (Same technique as CharacterContactTest.)
    foreach (range(1, 3) as $attempt) {
        $page->script("(document.querySelector('.max-h-96.overflow-auto') ?? document.body).scrollIntoView({ block: 'center' })");
        $page->waitForEvent('networkidle');
    }

    // The first history corporation's name appearing is proof the axios/Ziggy-free load path populated
    // the timeline and its ResolveIdToName resolved.
    $page->waitForText($historyCorporations->first()->name);

    snap($page, "recruitment-corporation-history-{$device}");
})->with(['desktop', 'iphone']);
