<?php

use Faker\Factory;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\RefreshToken;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

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
uses(TestCase::class)->in('Integration', 'Unit');
//uses(TestCase::class)->in('Unit');

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

    // now re-register all the roles and permissions
    app()->make(PermissionRegistrar::class)->registerPermissions();
}

function updateRefreshTokenWithScopes(\Seatplus\Eveapi\Models\RefreshToken $refreshToken, array $scopes): RefreshToken
{
    $helper_token = RefreshToken::factory()->scopes($scopes)->make([
        'character_id' => $refreshToken->character_id
    ]);

    $refreshToken->token = $helper_token->token;
    $refreshToken->save();

    return $refreshToken;
}