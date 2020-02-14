<?php

namespace Seatplus\Web\Tests\Unit\ConfigurationController;

use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class CommandControllerTest extends TestCase
{

    public function testIfPostCacheClearClearsCache()
    {


        $route = route('cache.clear');

        // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
        $this->app->instance('path.public', __DIR__ .'/../src/public');


        cache(['key' => 'value'], now()->addCenturies(1));

        $this->assertEquals('value', cache('key'));

        $permission = Permission::findOrCreate('superuser');

        $this->test_user->givePermissionTo($permission);

        // now re-register all the roles and permissions
        $this->app->make(PermissionRegistrar::class)->registerPermissions();

        $response = $this->actingAs($this->test_user)
            ->post($route)->assertOk();

        $this->assertNotEquals('value', cache('key'));

    }

}
