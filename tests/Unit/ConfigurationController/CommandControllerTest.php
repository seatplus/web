<?php

namespace Seatplus\Web\Tests\Unit\ConfigurationController;

use Seatplus\Web\Tests\TestCase;

class CommandControllerTest extends TestCase
{

    /**
     * @test
     */
    public function postClearCache()
    {
        $route = route('cache.clear');



        //$this->post($route)->assertForbidden();
    }

}
