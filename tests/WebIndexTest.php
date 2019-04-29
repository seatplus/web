<?php


namespace Seatplus\Web\Tests;

class WebIndexTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/test');

        $response->assertSeeText('test');
        $this->assertTrue(true);
    }
}