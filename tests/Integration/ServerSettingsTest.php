<?php


namespace Seatplus\Web\Tests\Integration;


use Seatplus\Auth\Models\User;
use Seatplus\Web\Tests\TestCase;

class ServerSettingsTest extends TestCase
{
    /** @test */
    public function it_has_users_list()
    {
        $response = $this->actingAs($this->test_user)
            ->get(route('server.settings'));

        $response->assertComponent('Configuration/UserList');
    }

    /** @test */
    public function it_has_server_scopes()
    {
        $response = $this->actingAs($this->test_user)
            ->get(route('settings.scopes'));

        $response->assertComponent('Configuration/ScopeSettings');
    }

    /** @test */
    public function one_can_impersionate()
    {
        $user_two = factory(User::class)->create();

        $response = $this->actingAs($this->test_user)
            ->get(route('impersonate.start', $user_two->id));

        $this->assertAuthenticatedAs($user_two);
    }

    /** @test */
    public function one_can_stop_impersionate()
    {
        $user_two = factory(User::class)->create();

       $this->actingAs($this->test_user)
            ->get(route('impersonate.start', $user_two->id));

        $this->assertAuthenticatedAs($user_two);

        $this->actingAs($user_two)
            ->get(route('impersonate.stop'));

        $this->assertAuthenticatedAs($this->test_user);
    }

}
