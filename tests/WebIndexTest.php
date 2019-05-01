<?php


namespace Seatplus\Web\Tests;

class WebIndexTest extends TestCase
{
    /** @test */
    public function redirectsToLoginIfUnauthorized()
    {
        $response = $this->get('/home');

        $response->assertRedirect('auth/login');
    }
}