<?php

use Faker\Factory;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Web\Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

/** @link https://pestphp.com/docs/underlying-test-case */
uses(TestCase::class)->in('Feature', 'Unit');
// uses(TestCase::class);

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

/** @link https://pestphp.com/docs/expectations#custom-expectations */

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/** @link https://pestphp.com/docs/helpers */
function faker()
{
    return Factory::create();
}

function assignPermissionToTestUser(array|string $permission_strings)
{
    $permission_strings = is_array($permission_strings) ? $permission_strings : [$permission_strings];

    foreach ($permission_strings as $string) {
        $permission = Permission::findOrCreate($string);

        test()->test_user->givePermissionTo($permission);
    }
}

function assignPermission(User $user, array|string $permission_strings): void
{
    $permission_strings = is_array($permission_strings) ? $permission_strings : [$permission_strings];

    foreach ($permission_strings as $string) {
        $user->givePermissionTo(Permission::findOrCreate($string));
    }
}

function updateRefreshTokenWithScopes(RefreshToken $refreshToken, array $scopes): RefreshToken
{
    $helper_token = RefreshToken::factory()->scopes($scopes)->make([
        'character_id' => $refreshToken->character_id,
    ]);

    $refreshToken->token = $helper_token->token;
    $refreshToken->save();

    return $refreshToken;
}

/**
 * Create a role via HTTP, set its affiliations, optionally assign permissions, and add a member.
 *
 * Uses the ACL HTTP endpoints so tests exercise the real request/response cycle.
 * Permissions are still assigned directly — no HTTP endpoint exists for that.
 *
 * @param  array<array{entity_id: int, entity_type: string, affiliation_type: string}>  $affiliations
 * @param  string[]  $permissions
 */
function createRoleViaHttp(
    string $roleName,
    array $affiliations,
    User $member,
    array $permissions = [],
    string $roleType = 'manual',
): Role {
    test()->actingAs(test()->superuser)
        ->followingRedirects()
        ->postJson(route('acl.create'), ['name' => $roleName]);

    $role = Role::findByName($roleName);

    if (! empty($affiliations)) {
        test()->actingAs(test()->superuser)
            ->postJson(route('acl.update.'.$roleType, $role->id), ['affiliated' => $affiliations])
            ->assertRedirect();
        $role->refresh();
    }

    foreach ($permissions as $permissionName) {
        $role->givePermissionTo(Permission::findOrCreate($permissionName));
    }

    test()->actingAs(test()->superuser)
        ->post(route('acl.member.add', [$role->id, $member->id]))
        ->assertRedirect();

    return $role->fresh();
}
