<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;

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

if (! function_exists('complianceMember')) {
    /**
     * Create a member User owning a single character that belongs to the given corporation, so the
     * user shows up in that corporation's compliance list. Mirrors actingAsCharacter() but for a
     * plain (non-acting) member: the CharacterInfo factory places the character in a random corp,
     * so repoint its affiliation at the target corporation.
     */
    function complianceMember(int $corporationId): CharacterInfo
    {
        $character = CharacterInfo::factory()->create();
        $character->characterAffiliation->update(['corporation_id' => $corporationId]);

        $user = new User;
        $user->main_character_id = $character->character_id;
        $user->save();

        CharacterUser::create([
            'user_id' => $user->getKey(),
            'character_id' => $character->character_id,
            'character_owner_hash' => sha1((string) $character->character_id),
        ]);

        return $character->refresh();
    }
}

/*
 * Corporation member-compliance browser test.
 *
 * Access is granted by the in-game Director corp role (giveCorporationRole) — not superuser —
 * which CanUserService accepts, mirroring a real director reviewing their corp's SSO-scope
 * compliance. The corporation only surfaces on the index once it carries an SsoScopes requirement,
 * so one is attached. The member list itself is loaded asynchronously (fetch → getJson against the
 * Wayfinder `getCorporationCompliance` endpoint, which now returns ->get() instead of ->paginate()),
 * so we wait for a row to resolve before asserting / screenshotting. Provisioning helpers live in
 * core's tests/Pest.php (actingAsCharacter / giveCorporationRole).
 */

uses(RefreshDatabase::class);

it('lists corporation members and their sso scope compliance', function (string $device) {
    $character = actingAsCharacter();
    $corporationId = $character->corporation->corporation_id;

    // Director corp role → grants member-compliance access (no superuser / Spatie permission).
    giveCorporationRole($character);

    // A corporation only appears on the compliance index once it has an SsoScopes requirement.
    // Require a scope the members do not hold so they render as non-compliant ("renegades").
    SsoScopes::updateOrCreate(
        ['morphable_id' => $corporationId],
        [
            'morphable_type' => CorporationInfo::class,
            'type' => 'default',
            'selected_scopes' => ['esi-assets.read_assets.v1'],
        ],
    );

    // A couple of additional members so the list renders multiple rows.
    complianceMember($corporationId);
    complianceMember($corporationId);

    $page = deviceVisit($device, '/corporation/compliance');
    $page->assertNoSmoke();
    $page->assertSee('Corporation Member Compliance');

    // The member list is fetched asynchronously, so wait for the acting director's own row to
    // resolve before asserting the list rendered.
    $page->waitForText($character->name);
    $page->assertSee($character->name);
    $page->assertSee('Main Character');

    snap($page, "corporation-member-compliance-{$device}");
})->with(['desktop', 'iphone']);
