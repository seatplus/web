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

/*
 * Unified role hub browser tests, run against the assembled core app. Walks each persona through the
 * hub (/acl/hub index + /acl/hub/{id} page) and asserts what they may see and do:
 *   - admin (can configure), moderator, member, eligible non-member (apply/self-assign), non-member.
 * Provisioning via actingAsCharacter() (core tests/Pest.php); the acting character's corporation is
 * used as the eligibility criterion. Helpers are guarded so the file runs in isolation.
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
    /** The User account that owns $characterId. */
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

// ── Admin (can configure) ────────────────────────────────────────────────────────────────────────

it('shows an admin the all-groups section with a configure gear on the hub index', function (string $device) {
    $character = actingAsCharacter();
    grantAclView($character->character_id);
    grantAclAdmin($character->character_id);
    makeManualRole('Ops Team');

    $page = deviceVisit($device, '/acl/hub');
    $page->assertNoSmoke();

    $page->waitForText('All groups');
    $page->waitForText('Ops Team');
    // The card's gear links an admin straight to Configure.
    $page->assertScript("!!document.querySelector('a[title=\"Configure\"]')");

    snap($page, "hub-admin-index-{$device}");
})->with(['desktop', 'iphone']);

it('gives an admin the overview, members and configure tabs plus delete', function (string $device) {
    $character = actingAsCharacter();
    grantAclAdmin($character->character_id);
    $role = makeManualRole('Directors');

    $page = deviceVisit($device, '/acl/hub/'.$role->id);
    $page->assertNoSmoke();

    $page->waitForText('Overview');
    $page->assertSee('Members');
    $page->assertSee('Configure');
    // Delete is a persistent header action for admins.
    $page->assertSee('Delete group');

    snap($page, "hub-admin-page-{$device}");
})->with(['desktop', 'iphone']);

// ── Moderator ────────────────────────────────────────────────────────────────────────────────────

it('shows a moderator a manage-members gear for their group on the hub index', function (string $device) {
    $character = actingAsCharacter();
    $user = grantAclView($character->character_id);
    $role = makeManualRole('Recruiters');
    addRoleMember($role, $user, moderator: true);

    $page = deviceVisit($device, '/acl/hub');
    $page->assertNoSmoke();

    // A moderated group sits under "My groups"; its gear points at member management, not configure.
    $page->waitForText('My groups');
    $page->waitForText('Recruiters');
    $page->assertScript("!!document.querySelector('a[title=\"Manage members\"]')");
    $page->assertScript("!document.querySelector('a[title=\"Configure\"]')");

    snap($page, "hub-moderator-index-{$device}");
})->with(['desktop', 'iphone']);

it('gives a moderator the members tab but not configure or delete', function (string $device) {
    $character = actingAsCharacter();
    $user = userOfCharacter($character->character_id);
    $role = makeManualRole('Logistics');
    addRoleMember($role, $user, moderator: true);

    $page = deviceVisit($device, '/acl/hub/'.$role->id);
    $page->assertNoSmoke();

    $page->waitForText('Overview');
    // Management is available…
    $page->assertSee('Members');
    // …but configuration and deletion are not.
    $page->assertDontSee('Configure');
    $page->assertDontSee('Delete group');

    snap($page, "hub-moderator-page-{$device}");
})->with(['desktop', 'iphone']);

// ── Member ───────────────────────────────────────────────────────────────────────────────────────

it('shows a member only the overview with a leave action', function (string $device) {
    $character = actingAsCharacter();
    $user = userOfCharacter($character->character_id);
    $role = makeManualRole('Fleet Wing');
    addRoleMember($role, $user);

    $page = deviceVisit($device, '/acl/hub/'.$role->id);
    $page->assertNoSmoke();

    $page->waitForText('Overview');
    // A member can leave, but cannot manage or configure the group.
    $page->assertSee('Leave');
    $page->assertDontSee('Configure');
    $page->assertDontSee('Delete group');

    snap($page, "hub-member-page-{$device}");
})->with(['desktop', 'iphone']);

// ── Eligible non-member (apply / self-assign) ────────────────────────────────────────────────────

it('lets an eligible non-member join a self-service group from the hub index', function (string $device) {
    $character = actingAsCharacter();
    grantAclView($character->character_id);
    makeOptInRoleForCorporation('Fleet Operations', $character->corporation_id);

    $page = deviceVisit($device, '/acl/hub');
    $page->assertNoSmoke();

    $page->waitForText('Available to join');
    $page->waitForText('Fleet Operations');
    // Self-service → a Join affordance, and no management gear.
    $page->assertSee('Join');
    $page->assertScript("!document.querySelector('a[title=\"Configure\"]') && !document.querySelector('a[title=\"Manage members\"]')");

    snap($page, "hub-eligible-index-{$device}");
})->with(['desktop', 'iphone']);

// ── Non-member ───────────────────────────────────────────────────────────────────────────────────

it('hides a group a non-member cannot join from the hub index', function (string $device) {
    $character = actingAsCharacter();
    grantAclView($character->character_id);
    // A managed group the user neither belongs to, moderates, nor can join, and is not an admin of.
    makeManualRole('Secret Ops');

    $page = deviceVisit($device, '/acl/hub');
    $page->assertNoSmoke();

    $page->waitForText('Group hub');
    $page->assertDontSee('Secret Ops');

    snap($page, "hub-nonmember-index-{$device}");
})->with(['desktop', 'iphone']);
