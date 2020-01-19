<?php


namespace Seatplus\Web\Tests\Integration;

use Seatplus\Web\Tests\TestCase;

class FlashMessagesTest extends TestCase
{
    /** @test */
    public function seeSuccessFlashMessages()
    {
        $response = $this->withSession(['success','SuccessFlashMessage'])
            ->get('auth/login');

        $response->assertHasProp('flash.success');
    }

    /** @test */
    public function seeErrorFlashMessages()
    {
        $response = $this->withSession(['error','ErrorFlashMessage'])
            ->get('auth/login');

        $response->assertHasProp('flash.error');
    }

    /** @test */
    public function seeWarningFlashMessages()
    {
        $response = $this->withSession(['warning','WarningFlashMessage'])
            ->get('auth/login');

        $response->assertHasProp('flash.warning');

    }

    /** @test */
    public function seeInfoFlashMessages()
    {
        $response = $this->withSession(['info','InfoFlashMessage'])
            ->get('auth/login');

        $response->assertHasProp('flash.info');

    }
}
