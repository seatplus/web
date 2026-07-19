<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationMemberTracking;
use Seatplus\Eveapi\Models\SsoScopes;

/*
 * Browser tests for observation during employment (Personnel → Observation), run against the
 * assembled core app. A director observes their corporation's members (compliance + activity) and
 * inspects one across the shared tabs. Real logged-in character comes from core's actingAsCharacter().
 */

uses(RefreshDatabase::class);

if (! function_exists('deviceVisit')) {
    function deviceVisit(string $device, string $url, array $options = []): mixed
    {
        if ($device === 'iphone') {
            return new PendingAwaitablePage(Playwright::defaultBrowserType(), Device::IPHONE_15, $url, $options);
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

if (! function_exists('makeObserverOfCorporation')) {
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
}

if (! function_exists('seedObservableCorporation')) {
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
}

// ═══ Observation ═══════════════════════════════════════════════════════════════════════════════

it('observation — a director sees a corporation\'s members with compliance and activity', function (string $device) {
    $character = actingAsCharacter();
    makeObserverOfCorporation($character);
    seedObservableCorporation($character);
    cache()->flush();

    $page = deviceVisit($device, '/employment');
    $page->assertNoSmoke();

    $page->waitForText('Observation');
    // Members are fetched asynchronously per corporation — wait for the member row to resolve.
    $page->waitForText('characters compliant');

    snap($page, "observation-{$device}");
})->with(['desktop', 'iphone']);

it('observation — a director inspects a member', function (string $device) {
    $character = actingAsCharacter();
    $user = makeObserverOfCorporation($character);
    seedObservableCorporation($character);
    cache()->flush();

    $page = deviceVisit($device, "/employment/{$character->corporation_id}/member/{$user->getKey()}");
    $page->assertNoSmoke();

    // The inspect header names the member; the shared inspection tabs render below it. (The Skills /
    // Contacts / Mails tab data is wired up on the multi-character branch, so only the default view is
    // asserted here.)
    $page->waitForText('Inspect');

    snap($page, "observation-inspect-{$device}");
})->with(['desktop', 'iphone']);
