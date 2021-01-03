<?php


namespace Seatplus\Web\Tests\Integration;

use Inertia\Inertia;
use Seatplus\Web\Tests\TestCase;

class FlashMessagesTest extends TestCase
{

    public function setUp(): void
    {
       parent::setUp();

        Inertia::share([
            'flash' => function () {
                return [
                    'success' => session()->pull('success'),
                    'info' => session()->pull('info'),
                    'warning' => session()->pull('warning'),
                    'error' => session()->pull('error'),
                ];
            },
        ]);
    }

    /** @test */
    public function seeSuccessFlashMessages()
    {
        $response = $this->withSession(['success','SuccessFlashMessage'])
            ->get('auth/login');

        $response->assertInertiaHas('flash.success');
    }

    /** @test */
    public function seeErrorFlashMessages()
    {
        $response = $this->withSession(['error','ErrorFlashMessage'])
            ->get('auth/login');

        $response->assertInertiaHas('flash.error');
    }

    /** @test */
    public function seeWarningFlashMessages()
    {
        $response = $this->withSession(['warning','WarningFlashMessage'])
            ->get('auth/login');

        $response->assertInertiaHas('flash.warning');

    }

    /** @test */
    public function seeInfoFlashMessages()
    {
        $response = $this->withSession(['info','InfoFlashMessage'])
            ->get('auth/login');

        $response->assertInertiaHas('flash.info');

    }
}
