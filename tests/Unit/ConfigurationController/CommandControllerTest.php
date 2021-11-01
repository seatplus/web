<?php

use Seatplus\Auth\Models\Permissions\Permission;
use Spatie\Permission\PermissionRegistrar;

test('if post cache clear clears cache', function () {


    $route = route('cache.clear');

    // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
    app()->instance('path.public', __DIR__ .'/../src/public');

    $permission = Permission::findOrCreate('superuser');

    test()->test_user->givePermissionTo($permission);

    // now re-register all the roles and permissions
    app()->make(PermissionRegistrar::class)->registerPermissions();

    \Illuminate\Support\Facades\Artisan::shouldReceive('call')
        ->once()
        ->with('seatplus:cache:clear --force')
        ->andReturn(1);

   $response = test()->actingAs(test()->test_user)
        ->post($route)->assertOk();

});
