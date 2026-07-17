<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Recruitment\Enlistments;

/*
 * Recruitment applications browser test, run against the assembled core app.
 *
 * A recruiter (in-game Director corp role → the affiliation path GetRecruitmentIndexController
 * and CheckAffiliationForApplication both accept, plus the `can accept or deny applications`
 * permission) opens an enlistment for their own corporation and looks at the applications.
 *
 * The pending / closed tables are now native Inertia <InfiniteScroll> lists over per-corporation
 * scroll props (open_<corp>_<step> / closed_<corp>) — no axios. This proves both tables render
 * their scroll body and merge at least the first page, and that the redesigned review page loads.
 * Provisioning helpers (actingAsCharacter / giveCorporationRole) live in core's tests/Browser/Pest.php.
 */

uses(RefreshDatabase::class);

if (! function_exists('deviceVisit')) {
    function deviceVisit(string $device, string $url, array $options = []): mixed
    {
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
    function snap($page, string $name): void
    {
        $page->script("document.querySelectorAll('img').forEach((i) => { i.loading = 'eager'; });");
        $page->waitForEvent('networkidle');
        $page->screenshot(true, $name);
    }
}

if (! function_exists('userOfCharacter')) {
    function userOfCharacter(int $characterId): User
    {
        return CharacterUser::query()->where('character_id', $characterId)->firstOrFail()->user;
    }
}

if (! function_exists('makeApplicationForCorporation')) {
    /** Seed a distinct applicant user (with characters) and an application to $corporationId. */
    function makeApplicationForCorporation(int $corporationId, string $status): Application
    {
        $applicant = User::factory()->create();

        return Application::factory()->create([
            'corporation_id' => $corporationId,
            'applicationable_type' => User::class,
            'applicationable_id' => $applicant->getKey(),
            'status' => $status,
        ]);
    }
}

it('renders the pending and closed application tables and the review page', function (string $device) {
    $character = actingAsCharacter();
    $corporationId = $character->corporation->corporation_id;

    // Director corp role grants recruitment access + corp affiliation; the permission mirrors the
    // real recruiter grant and the `can accept or deny applications` gate on the routes.
    giveCorporationRole($character);
    userOfCharacter($character->character_id)
        ->givePermissionTo(Permission::findOrCreate('can accept or deny applications'));

    // Open the corporation for recruitment (single default "Open" step → the index shows an
    // "Open" tab and a "Closed" tab).
    Enlistments::create([
        'corporation_id' => $corporationId,
        'type' => 'user',
    ]);

    // One open application (0 decisions → open_<corp>_0) and one closed one (closed_<corp>).
    $openApplication = makeApplicationForCorporation($corporationId, 'open');
    makeApplicationForCorporation($corporationId, 'rejected');

    $page = deviceVisit($device, '/corporation/recruitment');
    $page->assertNoSmoke();

    // The enlistment card renders the corporation directly (EntityBlock, no async id lookup).
    $page->waitForText($character->corporation->name);

    // Pending table: its <InfiniteScroll> scroll body loads at least the open application's row.
    // assertScript auto-polls until the deferred open_<corp>_0 scroll prop has merged in.
    $page->assertScript("document.querySelectorAll('#open-body-{$corporationId}-0 > div').length > 0");

    // The closed tab is a hover/click nav on desktop; on mobile it is a native <select>, which
    // Playwright drives differently — assert the closed table via the desktop nav only.
    if ($device === 'desktop') {
        $page->click('Closed');
        $page->assertScript("document.querySelectorAll('#closed-body-{$corporationId} > div').length > 0");
        $page->assertSee('Activity Log');
    }

    snap($page, "recruitment-applications-{$device}");

    // Redesigned review page: CardWithHeader + useForm decision form.
    $review = deviceVisit($device, "/corporation/recruitment/application/{$openApplication->id}");
    $review->assertNoSmoke();
    $review->waitForText('Decision');
    $review->assertSee('Accept application');
    $review->assertSee('Reject application');
    $review->assertSee('Submit review');

    snap($review, "recruitment-application-review-{$device}");
})->with(['desktop', 'iphone']);
