<?php

use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

uses(TestCase::class);

test('if post cache clear clears cache', function () {


    $route = route('cache.clear');

    // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
    test()->app->instance('path.public', __DIR__ .'/../src/public');


    cache(['key' => 'value'], now()->addCenturies(1));

    test()->assertEquals('value', cache('key'));

    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    test()->app->make(PermissionRegistrar::class)->registerPermissions();

    $response = test()->actingAs(test()->test_user)
        ->post($route)->assertOk();

    test()->assertNotEquals('value', cache('key'));

});
