<?php


namespace Seatplus\Web\Tests\Integration;


use Inertia\Testing\Assert;
use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Web\Tests\TestCase;

class UserSettingsTest extends TestCase
{
    /** @test */
    public function it_has_user_settings()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('user.settings'));

        //$response->assertInertia('Configuration/UserSettings');
        $response->assertInertia( fn (Assert $page) => $page->component('Configuration/UserSettings'));
    }

    /** @test */
    public function one_can_update_main_character()
    {
        $secondary_character = Event::fakeFor(fn() => CharacterUser::factory()->make());

        $this->test_user->character_users()->save($secondary_character);

        $this->assertNotEquals($this->test_user->main_character, $secondary_character->character);

        $this->actingAs($this->test_user)
            ->json('POST', route('update.main_character'), [
                "character_id" => $secondary_character->character_id,
            ]);

        $this->assertEquals($this->test_user->refresh()->main_character, $secondary_character->character);

    }

}
