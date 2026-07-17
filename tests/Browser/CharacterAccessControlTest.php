<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Enums\AffiliationType;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Affiliation;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

/*
 * Create-group wizard browser tests, run against the assembled core app. The discover / configure /
 * moderate flows now live on the unified hub (see RoleHubTest); this file covers the one surface the
 * hub doesn't — the guided creation wizard (/acl/create), which lands on the new group's hub.
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

if (! function_exists('grantAclAdmin')) {
    function grantAclAdmin(int $characterId): User
    {
        $user = userOfCharacter($characterId);
        $user->givePermissionTo(Permission::findOrCreate('administrate access control groups'));

        return $user;
    }
}

it('creates a group through the guided wizard (admin)', function (string $device) {
    $character = actingAsCharacter();
    grantAclAdmin($character->character_id);

    // A real corporation so its applies-to pill renders a real logo (not the CDN placeholder).
    $corporation = CorporationInfo::factory()->create([
        'corporation_id' => 98008630,
        'name' => 'Thunderwaffe',
    ]);

    $page = deviceVisit($device, '/acl/create');
    $page->assertNoSmoke();
    $page->waitForText('New group');

    // Step 1 — Name.
    $page->type('role-name', 'Logistics');
    $page->click('Next');

    // Step 2 — Join method: pick "Managed" (no eligibility step for manual).
    $page->waitForText('Managed');
    $page->click('Managed');
    $page->click('Next');

    // Step — Applies to. The entity pickers are ESI autosuggest (need a live token), so a specific
    // corp can't be selected in a browser test; the real-corp scope is attached via the model below.
    $page->waitForText('Only these');
    $page->click('Next');

    // Step — Permissions: grant one (the list is DB-backed, so this IS driven through the UI).
    $page->waitForText('view access control');
    $page->click('view access control');
    $page->click('Next');
    $page->waitForText('Logistics'); // review summary shows the name

    // Create → lands on the new group's hub.
    $page->click('Create group');
    $page->assertScript("document.body.innerText.includes('Logistics')");
    $page->assertNoSmoke();

    // The wizard created the group with the permission; attach the applies-to scope the ESI picker
    // can't set in a test — INVERSE ("everyone except Thunderwaffe") to a real corporation — then
    // reopen the hub so the screenshot shows a fully-configured group (real logo pill + permission).
    $role = Role::firstWhere('name', 'Logistics');
    expect($role->permissions->pluck('name'))->toContain('view access control');
    Affiliation::create([
        'role_id' => $role->id,
        'affiliatable_id' => $corporation->corporation_id,
        'affiliatable_type' => CorporationInfo::class,
        'type' => AffiliationType::INVERSE->value,
    ]);

    $page = deviceVisit($device, '/acl/hub/'.$role->id);
    $page->assertNoSmoke();
    $page->waitForText('Logistics');
    $page->waitForText('Thunderwaffe');
    $page->waitForText('view access control');

    snap($page, "acl-create-wizard-{$device}");
})->with(['desktop', 'iphone']);

it('creates an open-to-all group through the wizard (admin)', function (string $device) {
    $character = actingAsCharacter();
    grantAclAdmin($character->character_id);

    $page = deviceVisit($device, '/acl/create');
    $page->assertNoSmoke();
    $page->waitForText('New group');

    // Step 1 — Name.
    $page->type('role-name', 'Everyone');
    $page->click('Next');

    // Step 2 — Join method: "Self-service" (opt-in → the eligibility step appears).
    $page->waitForText('Self-service');
    $page->click('Self-service');
    $page->click('Next');

    // Step — Eligibility: toggle "Anyone can join" (Doomheim sentinel).
    $page->waitForText('Anyone can join');
    $page->click('Anyone can join');
    $page->click('Next');

    // Step — Applies to: toggle "Everything".
    $page->waitForText('Everything');
    $page->click('Everything');
    $page->click('Next');

    // Step — Permissions (leave empty) → Review.
    $page->click('Next');
    $page->waitForText('Everyone'); // review summary shows the name

    // Create → lands on the new group's hub.
    $page->click('Create group');
    $page->assertScript("document.body.innerText.includes('Everyone')");
    $page->assertNoSmoke();

    snap($page, "acl-create-wizard-everyone-{$device}");
})->with(['desktop', 'iphone']);
