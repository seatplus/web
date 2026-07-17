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
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

/*
 * Unified role hub browser tests, run against the assembled core app. Covers the 3 management
 * personas (admin / moderator / member) across each hub surface — the index, and the Overview,
 * Members and Configure tabs — plus the eligible-non-member and non-member cases on the index.
 * Tabs are reached by ?tab= so the tests also prove the gate: a persona that requests a tab it may
 * not see falls back to Overview. Provisioning via actingAsCharacter() (core tests/Pest.php).
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

if (! function_exists('grantAclView')) {
    function grantAclView(int $characterId): User
    {
        $user = userOfCharacter($characterId);
        $user->givePermissionTo(Permission::findOrCreate('view access control'));

        return $user;
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

if (! function_exists('makeManualRole')) {
    function makeManualRole(string $name): Role
    {
        $role = Role::findById(Role::create(['name' => $name])->id);
        $role->update(['type' => RoleType::MANUAL]);

        return $role;
    }
}

if (! function_exists('makeOnRequestRole')) {
    function makeOnRequestRole(string $name): Role
    {
        $role = Role::findById(Role::create(['name' => $name])->id);
        $role->update(['type' => RoleType::ON_REQUEST]);

        return $role;
    }
}

if (! function_exists('makeOptInRoleForCorporation')) {
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

if (! function_exists('addRoleMember')) {
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
}

if (! function_exists('makeApplicant')) {
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
}

// ═══ Index (/acl/hub) ═════════════════════════════════════════════════════════════════════════

it('index — an admin sees all groups with a configure gear', function (string $device) {
    $character = actingAsCharacter();
    // No view permission granted: the broadened index gate admits admins on `administrate` alone.
    grantAclAdmin($character->character_id);
    makeManualRole('Ops Team');

    $page = deviceVisit($device, '/acl/hub');
    $page->assertNoSmoke();

    $page->waitForText('All groups');
    $page->waitForText('Ops Team');
    $page->assertScript("!!document.querySelector('a[title=\"Configure\"]')");

    snap($page, "hub-index-admin-{$device}");
})->with(['desktop', 'iphone']);

it('index — a moderator sees their group with a manage-members gear', function (string $device) {
    $character = actingAsCharacter();
    // No view permission: a per-role moderator reaches the index via the broadened gate.
    $user = userOfCharacter($character->character_id);
    $role = makeManualRole('Recruiters');
    addRoleMember($role, $user, moderator: true);

    $page = deviceVisit($device, '/acl/hub');
    $page->assertNoSmoke();

    $page->waitForText('My groups');
    $page->waitForText('Recruiters');
    $page->assertScript("!!document.querySelector('a[title=\"Manage members\"]')");
    $page->assertScript("!document.querySelector('a[title=\"Configure\"]')");

    snap($page, "hub-index-moderator-{$device}");
})->with(['desktop', 'iphone']);

it('index — a member sees their group with a leave action and no gear', function (string $device) {
    $character = actingAsCharacter();
    $user = grantAclView($character->character_id);
    $role = makeManualRole('Fleet Wing');
    addRoleMember($role, $user);

    $page = deviceVisit($device, '/acl/hub');
    $page->assertNoSmoke();

    $page->waitForText('My groups');
    $page->waitForText('Fleet Wing');
    $page->assertSee('Leave');
    $page->assertScript("!document.querySelector('a[title=\"Configure\"]') && !document.querySelector('a[title=\"Manage members\"]')");

    snap($page, "hub-index-member-{$device}");
})->with(['desktop', 'iphone']);

it('index — an eligible non-member can join a self-service group', function (string $device) {
    $character = actingAsCharacter();
    grantAclView($character->character_id);
    makeOptInRoleForCorporation('Fleet Operations', $character->corporation_id);

    $page = deviceVisit($device, '/acl/hub');
    $page->assertNoSmoke();

    $page->waitForText('Available to join');
    $page->waitForText('Fleet Operations');
    $page->assertSee('Join');
    $page->assertScript("!document.querySelector('a[title=\"Configure\"]') && !document.querySelector('a[title=\"Manage members\"]')");

    snap($page, "hub-index-eligible-{$device}");
})->with(['desktop', 'iphone']);

it('index — a non-member does not see a group they cannot join', function (string $device) {
    $character = actingAsCharacter();
    grantAclView($character->character_id);
    makeManualRole('Secret Ops');

    $page = deviceVisit($device, '/acl/hub');
    $page->assertNoSmoke();

    $page->waitForText('Group hub');
    $page->assertDontSee('Secret Ops');

    snap($page, "hub-index-nonmember-{$device}");
})->with(['desktop', 'iphone']);

// ═══ Overview tab ═════════════════════════════════════════════════════════════════════════════

it('overview — an admin sees all three tabs', function (string $device) {
    $character = actingAsCharacter();
    grantAclAdmin($character->character_id);
    $role = makeManualRole('Directors');

    $page = deviceVisit($device, '/acl/hub/'.$role->id);
    $page->assertNoSmoke();

    $page->waitForText('Overview');
    $page->assertSee('Members');
    $page->assertSee('Configure');

    snap($page, "hub-overview-admin-{$device}");
})->with(['desktop', 'iphone']);

it('overview — a moderator sees the members tab but not configure', function (string $device) {
    $character = actingAsCharacter();
    $user = userOfCharacter($character->character_id);
    $role = makeOnRequestRole('Logistics');
    addRoleMember($role, $user, moderator: true);

    $page = deviceVisit($device, '/acl/hub/'.$role->id);
    $page->assertNoSmoke();

    $page->waitForText('Overview');
    $page->assertSee('Members');
    $page->assertDontSee('Configure');

    snap($page, "hub-overview-moderator-{$device}");
})->with(['desktop', 'iphone']);

it('overview — a member sees only overview with a leave action', function (string $device) {
    $character = actingAsCharacter();
    $user = userOfCharacter($character->character_id);
    $role = makeManualRole('Fleet Wing');
    addRoleMember($role, $user);

    $page = deviceVisit($device, '/acl/hub/'.$role->id);
    $page->assertNoSmoke();

    $page->waitForText('Overview');
    $page->assertSee('Leave');
    $page->assertDontSee('Configure');

    snap($page, "hub-overview-member-{$device}");
})->with(['desktop', 'iphone']);

// ═══ Members tab (?tab=members) ═══════════════════════════════════════════════════════════════

it('members — an admin can approve a pending application', function (string $device) {
    $character = actingAsCharacter();
    grantAclAdmin($character->character_id);
    $role = makeOnRequestRole('Recruiters');
    makeApplicant($role, 'Hopeful Pilot');

    $page = deviceVisit($device, '/acl/hub/'.$role->id.'?tab=members');
    $page->assertNoSmoke();

    $page->waitForText('Pending applications');
    $page->waitForText('Hopeful Pilot');
    $page->click('Approve');
    $page->assertScript("(document.body.innerText.includes('Members') && document.body.innerText.includes('Hopeful Pilot'))");
    $page->assertNoSmoke();

    snap($page, "hub-members-admin-{$device}");
})->with(['desktop', 'iphone']);

it('members — a moderator sees the applications to review', function (string $device) {
    $character = actingAsCharacter();
    $user = userOfCharacter($character->character_id);
    $role = makeOnRequestRole('Recruiters');
    addRoleMember($role, $user, moderator: true);
    makeApplicant($role, 'Hopeful Pilot');

    $page = deviceVisit($device, '/acl/hub/'.$role->id.'?tab=members');
    $page->assertNoSmoke();

    $page->waitForText('Pending applications');
    $page->waitForText('Hopeful Pilot');
    $page->assertSee('Approve');

    snap($page, "hub-members-moderator-{$device}");
})->with(['desktop', 'iphone']);

it('members — a member is redirected to overview and sees no management', function (string $device) {
    $character = actingAsCharacter();
    $user = userOfCharacter($character->character_id);
    $role = makeManualRole('Fleet Wing');
    addRoleMember($role, $user);

    // Requests the members tab but may not manage → the hub falls back to Overview.
    $page = deviceVisit($device, '/acl/hub/'.$role->id.'?tab=members');
    $page->assertNoSmoke();

    $page->waitForText('Overview');
    $page->assertSee('Leave');
    $page->assertDontSee('Pending applications');
    $page->assertDontSee('No members yet.');

    snap($page, "hub-members-member-{$device}");
})->with(['desktop', 'iphone']);

// ═══ Configure tab (?tab=configure) ═══════════════════════════════════════════════════════════

it('configure — an admin sees the configuration form', function (string $device) {
    $character = actingAsCharacter();
    grantAclAdmin($character->character_id);
    $role = makeManualRole('Directors');

    $page = deviceVisit($device, '/acl/hub/'.$role->id.'?tab=configure');
    $page->assertNoSmoke();

    // Distinctive configure-form section headings.
    $page->waitForText('Membership');
    $page->assertSee('Authorization');
    $page->assertSee('Delete group');

    snap($page, "hub-configure-admin-{$device}");
})->with(['desktop', 'iphone']);

it('configure — a moderator cannot configure and lands on overview', function (string $device) {
    $character = actingAsCharacter();
    $user = userOfCharacter($character->character_id);
    $role = makeOnRequestRole('Logistics');
    addRoleMember($role, $user, moderator: true);

    $page = deviceVisit($device, '/acl/hub/'.$role->id.'?tab=configure');
    $page->assertNoSmoke();

    $page->waitForText('Overview');
    $page->assertDontSee('Authorization');
    $page->assertDontSee('Delete group');

    snap($page, "hub-configure-moderator-{$device}");
})->with(['desktop', 'iphone']);

it('configure — a member cannot configure and lands on overview', function (string $device) {
    $character = actingAsCharacter();
    $user = userOfCharacter($character->character_id);
    $role = makeManualRole('Fleet Wing');
    addRoleMember($role, $user);

    $page = deviceVisit($device, '/acl/hub/'.$role->id.'?tab=configure');
    $page->assertNoSmoke();

    $page->waitForText('Overview');
    $page->assertDontSee('Authorization');

    snap($page, "hub-configure-member-{$device}");
})->with(['desktop', 'iphone']);
