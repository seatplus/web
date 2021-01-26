<?php


namespace Seatplus\Web\Tests;

use ClaudioDekker\Inertia\Assert;
use Inertia\Inertia;

class WebIndexTest extends TestCase
{

    /** @test */
    public function redirectsToLoginIfUnauthorized()
    {
        $response = $this->get('/home');

        $response->assertRedirect('auth/login');
    }

    /** @test */
    public function redirectsToLoginVueComponentIfUnauthorized()
    {
        // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
        //$this->app->instance('path.public', __DIR__ .'/../src/public');

        $response = $this->followingRedirects()
            ->get('/home');

        $response->assertInertia( fn (Assert $page) => $page->component('Auth/Login'));
    }

    /** @test */
    public function redirectsToHomeIfAuthorized()
    {
        // Change path.public from Laravel IoC Container to point to proper laravel mix manifest.
        $this->app->instance('path.public', __DIR__ .'/../src/public');

        $response = $this->actingAs($this->test_user)
            ->get('/home');


        $response->assertInertia( fn (Assert $page) => $page->component('Dashboard/Index'));

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
