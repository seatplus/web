<?php


namespace Seatplus\Web\Tests\Integration;


use Inertia\Testing\Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\Models\Contracts\ContractItem;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class SkillsIntegrationTest extends TestCase
{
    public function setUp(): void
    {

        parent::setUp();

        $permission = Permission::findOrCreate('superuser');

        $this->test_user->givePermissionTo($permission);

        // now re-register all the roles and permissions
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }

    /** @test */
    public function hasDispatchableJob()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('character.skills'));

        $response->assertInertia( fn (Assert $page) => $page
            ->component('Character/Skill/Index')
            ->has('dispatchTransferObject')
        );
    }

    /** @test */
    public function oneGetSkillsPerCharacter()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('get.character.skills', $this->test_character->character_id))
            ->assertOk();

    }

    /** @test */
    public function oneGetSkillQueuePerCharacter()
    {

        $response = $this->actingAs($this->test_user)
            ->get(route('get.character.skill.queue', $this->test_character->character_id))
            ->assertOk();

    }


}
