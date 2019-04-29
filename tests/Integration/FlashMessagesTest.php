<?php


namespace Seatplus\Web\Tests\Integration;

use Seatplus\Web\Tests\TestCase;

class FlashMessagesTest extends TestCase
{
    /** @test */
    public function seeFlashMessages()
    {
        $response = $this->get('/test');

        $response->assertSeeText('test');
    }
}