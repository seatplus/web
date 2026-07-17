<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
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

it('renders a recruit corporation history through the recruitment review page', function () {
    // The logged-in reviewer. Superuser bypasses CheckAffiliationForApplication, so no role /
    // affiliation plumbing is needed to open someone else's application.
    $reviewerCharacter = actingAsCharacter();

    $reviewer = CharacterUser::query()
        ->where('character_id', $reviewerCharacter->character_id)
        ->firstOrFail()
        ->user;

    $reviewer->givePermissionTo(Permission::findOrCreate('superuser'));

    // The recruit whose (character-type) application is being reviewed. A real EVE id renders a
    // real portrait for the corporation-history card's EntityBlock header.
    $recruit = CharacterInfo::factory()->create(['character_id' => realCharacterId()]);
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

    $page = visit("/recruitment/application/{$application->id}");
    $page->assertNoSmoke();
    $page->assertSee('User Application');

    // The review page opens on the "Log" tab; switch to the corporation-history tab, which mounts
    // CorporationHistoryComponent and triggers its fetch-swap load.
    $page->click('Corporation History');

    // The first history corporation's name only appears once the fetch has resolved and the row's
    // ResolveIdToName has run — proof the axios/Ziggy-free load path populated the timeline.
    $page->waitForText($historyCorporations->first()->name);

    snap($page, 'recruitment-corporation-history');
});
