<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Auth\Enums\RoleType;
use Seatplus\Auth\Models\AccessControl\RoleMembership;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Corporation\CorporationInfo;

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

it('lists a group the eligible user can join under "Available to join"', function () {
    $character = actingAsCharacter();
    grantAclView($character->character_id);
    makeOptInRoleForCorporation('Fleet Operations', $character->corporation_id);

    $page = visit('/acl');
    $page->assertNoSmoke();

    $page->waitForText('Available to join');
    $page->waitForText('Fleet Operations');
    // Self-service group → a Join affordance is present.
    $page->assertSee('Join');

    $page->screenshot(true, 'acl-discover-available');
});

it('joins a self-service group and moves it into "My groups"', function () {
    $character = actingAsCharacter();
    grantAclView($character->character_id);
    makeOptInRoleForCorporation('Fleet Operations', $character->corporation_id);

    $page = visit('/acl');
    $page->assertNoSmoke();
    $page->waitForText('Fleet Operations');

    // Join instantly (opt-in); the card re-renders as a member under My groups.
    $page->click('Join');
    $page->assertScript("(document.body.innerText.includes('Fleet Operations') && document.body.innerText.includes('Member'))");
    $page->assertNoSmoke();

    $page->screenshot(true, 'acl-discover-joined');
});

it('renders a group the user already belongs to under "My groups"', function () {
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

    $page = visit('/acl');
    $page->assertNoSmoke();
    $page->waitForText('My groups');
    $page->waitForText('Directors');

    $page->screenshot(true, 'acl-discover-my-groups');
});

it('configures a group: switch join method and save (admin)', function () {
    $character = actingAsCharacter();
    grantAclAdmin($character->character_id);
    $role = Role::findById(Role::create(['name' => 'Ops Team'])->id);

    $page = visit('/manage_control_group/'.$role->id);
    $page->assertNoSmoke();

    // The two clearly-separated sections + the join-method picker render.
    $page->waitForText('Membership');
    $page->waitForText('Authorization');
    $page->waitForText('Self-service');

    // Switch join method and save (no entities needed — posts to acl.update.{type}).
    $page->click('Self-service');
    $page->click('Save');
    $page->assertNoSmoke();

    $page->screenshot(true, 'acl-configure');
});
