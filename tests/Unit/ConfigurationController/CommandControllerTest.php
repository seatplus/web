<?php

namespace Seatplus\Web\Tests\Unit\ConfigurationController;

use Illuminate\Support\Facades\Redis;
use Seatplus\Web\Tests\TestCase;

class CommandControllerTest extends TestCase
{

    /**
     * @test
     */
    public function testifPostCacheClearFlushesRedis()
    {
        $route = route('cache.clear');

        // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
        $this->app->instance('path.public', __DIR__ .'/../src/public');


        Redis::set('key', 'value');

        $this->assertEquals('value', Redis::get('key'));

        $this->actingAs($this->test_user)
            ->post($route)->assertOk();

        $this->assertNotEquals('value', Redis::get('key'));

    }

    public function testIfPostCacheClearClearsCache()
    {

        $route = route('cache.clear');

        // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
        $this->app->instance('path.public', __DIR__ .'/../src/public');


        cache(['key' => 'value'], now()->addCenturies(1));

        $this->assertEquals('value', cache('key'));

        $this->actingAs($this->test_user)
            ->post($route)->assertOk();

        $this->assertNotEquals('value', cache('key'));

    }

}
