<?php

namespace Seatplus\Web\Tests\Unit\ConfigurationController;

use Seatplus\Web\Tests\TestCase;

class CommandControllerTest extends TestCase
{

    public function testIfPostCacheClearClearsCache()
    {

        /*if(!env('APP_ENV') == 'testing')
            $this->markTestSkipped('this test is only made on travis, due to local issues');*/

        $route = route('cache.clear');

        // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
        $this->app->instance('path.public', __DIR__ .'/../src/public');


        cache(['key' => 'value'], now()->addCenturies(1));

        $this->assertEquals('value', cache('key'));

        $response = $this->actingAs($this->test_user)
            ->post($route)->assertOk();

        $this->assertNotEquals('value', cache('key'));

    }

}
