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

    /** @test */
    public function redirectsToLoginViewIfUnauthorized()
    {
        // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
        $this->app->instance('path.public', __DIR__ .'/../src/public');

        $this->followingRedirects()
            ->get('/home')
            ->assertViewIs('web::auth.login');
    }

    /** @test */
    public function redirectsToHomeIfAuthorized()
    {
        // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
        $this->app->instance('path.public', __DIR__ .'/../src/public');

        $response = $this->actingAs($this->test_user)
            ->get('/home');

        $response->assertSee('congratulation');
        $response->assertViewIs('web::home');

        $this->assertAuthenticatedAs($this->test_user);
        $this->assertTrue(auth()->check());
    }

    /** @test */
    public function logoutIfAuthorized()
    {
        // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
        $this->app->instance('path.public', __DIR__ .'/../src/public');

        $response = $this->actingAs($this->test_user)
            ->followingRedirects()
            ->get(route('auth.logout'));

        //$response->assertRedirect('auth/login');
        //$response->assertViewIs('web::auth.login');

        $this->assertFalse(auth()->check());

    }
}