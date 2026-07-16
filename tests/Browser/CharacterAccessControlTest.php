<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Browser\Api\PendingAwaitablePage;
use Pest\Browser\Enums\Device;
use Pest\Browser\Playwright\Playwright;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

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

/*
 * Access-control discover flow browser tests, run against the assembled core app.
 * Covers the redesigned /acl index: "My groups" vs "Available to join" segmentation and the
 * self-service Join action for an eligible user. Provisioning via actingAsCharacter()
 * (core tests/Pest.php); the acting character's corporation is used as the join criterion.
 */

uses(RefreshDatabase::class);

if (! function_exists('grantAclView')) {
    /** Give the user owning $characterId the 'view access control' permission and return them. */
    function grantAclView(int $characterId): User
    {
        $user = CharacterUser::query()->where('character_id', $characterId)->firstOrFail()->user;
        $user->givePermissionTo(Permission::findOrCreate('view access control'));

        return $user;
    }
}

if (! function_exists('grantAclAdmin')) {
    /** Give the user owning $characterId the 'administrate access control groups' permission. */
    function grantAclAdmin(int $characterId): User
    {
        $user = CharacterUser::query()->where('character_id', $characterId)->firstOrFail()->user;
        $user->givePermissionTo(Permission::findOrCreate('administrate access control groups'));

        return $user;
    }
}

if (! function_exists('makeOptInRoleForCorporation')) {
    /** A self-service (opt-in) group whose eligibility criterion is $corporationId. */
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
}

it('lists a group the eligible user can join under "Available to join"', function (string $device) {
    $character = actingAsCharacter();
    grantAclView($character->character_id);
    makeOptInRoleForCorporation('Fleet Operations', $character->corporation_id);

    $page = deviceVisit($device, '/acl');
    $page->assertNoSmoke();

    $page->waitForText('Available to join');
    $page->waitForText('Fleet Operations');
    // Self-service group → a Join affordance is present.
    $page->assertSee('Join');

    snap($page, "acl-discover-available-{$device}");
})->with(['desktop', 'iphone']);

it('joins a self-service group and moves it into "My groups"', function (string $device) {
    $character = actingAsCharacter();
    grantAclView($character->character_id);
    makeOptInRoleForCorporation('Fleet Operations', $character->corporation_id);

    $page = deviceVisit($device, '/acl');
    $page->assertNoSmoke();
    $page->waitForText('Fleet Operations');

    // Join instantly (opt-in); the card re-renders as a member under My groups.
    $page->click('Join');
    $page->assertScript("(document.body.innerText.includes('Fleet Operations') && document.body.innerText.includes('Member'))");
    $page->assertNoSmoke();

    snap($page, "acl-discover-joined-{$device}");
})->with(['desktop', 'iphone']);

it('renders a group the user already belongs to under "My groups"', function (string $device) {
    $character = actingAsCharacter();
    $user = grantAclView($character->character_id);

    $role = Role::findById(Role::create(['name' => 'Directors'])->id);
    $role->update(['type' => RoleType::MANUAL]);
    RoleMembership::create([
        'role_id' => $role->id,
        'entity_type' => User::class,
        'entity_id' => $user->getKey(),
        'status' => 'active',
    ]);

    $page = deviceVisit($device, '/acl');
    $page->assertNoSmoke();
    $page->waitForText('My groups');
    $page->waitForText('Directors');

    snap($page, "acl-discover-my-groups-{$device}");
})->with(['desktop', 'iphone']);

it('configures a group: switch join method and save (admin)', function (string $device) {
    $character = actingAsCharacter();
    grantAclAdmin($character->character_id);
    $role = Role::findById(Role::create(['name' => 'Ops Team'])->id);

    $page = deviceVisit($device, '/acl/manage_control_group/'.$role->id);
    $page->assertNoSmoke();

    // The two clearly-separated sections + the join-method picker render.
    $page->waitForText('Membership');
    $page->waitForText('Authorization');
    $page->waitForText('Self-service');

    // Switch join method and save (no entities needed — posts to acl.update.{type}).
    $page->click('Self-service');
    $page->click('Save');
    $page->assertNoSmoke();

    snap($page, "acl-configure-{$device}");
})->with(['desktop', 'iphone']);

it('creates a group through the guided wizard (admin)', function (string $device) {
    $character = actingAsCharacter();
    grantAclAdmin($character->character_id);

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

    // Step — Applies to (leave scope empty).
    $page->waitForText('Only these');
    $page->click('Next');

    // Step — Permissions (leave empty) → Review.
    $page->click('Next');
    $page->waitForText('Logistics'); // review summary shows the name

    // Create → lands on the new group's detail.
    $page->click('Create group');
    $page->assertScript("document.body.innerText.includes('Logistics')");
    $page->assertNoSmoke();

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

    // Create → lands on the new group's detail.
    $page->click('Create group');
    $page->assertScript("document.body.innerText.includes('Everyone')");
    $page->assertNoSmoke();

    snap($page, "acl-create-wizard-everyone-{$device}");
})->with(['desktop', 'iphone']);
