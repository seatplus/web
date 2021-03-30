<?php


namespace Seatplus\Web\Tests\Integration;


use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Tests\TestCase;

class SuperuserCommandTest extends TestCase
{

    public function setUp(): void
    {

        parent::setUp();

        // this assures that we have a user->characters relationship
        Event::fakeFor(fn() => CharacterUser::factory()->create(['user_id' => $this->test_user->id]));

    }

    /** @test */
    public function it_accepts_legal_user_id()
    {

        $this->artisan('seatplus:assign:superuser', ['characterName' => $this->test_user->characters->first()->name])
            ->expectsQuestion('Who should be superadmin?',$this->test_user->id)
            ->expectsQuestion('Do you wish to a continue?', 'y')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_does_not_accepts_illegal_user_id()
    {
        $this->assertDatabaseMissing('users', ['id' => $this->test_user->id + 1]);

        $this->artisan('seatplus:assign:superuser', ['characterName' => $this->test_user->characters->first()->name])
            ->expectsQuestion('Who should be superadmin?',$this->test_user->id + 1)
            ->assertExitCode(0);
    }

    /** @test */
    public function it_creates_role()
    {
        $this->assertDatabaseMissing('roles', ['name' => 'Superuser']);

        $this->artisan('seatplus:assign:superuser', ['characterName' => $this->test_user->characters->first()->name])
            ->expectsQuestion('Who should be superadmin?',$this->test_user->id)
            ->expectsQuestion('Do you wish to a continue?', 'y')
            ->assertExitCode(0);

        $this->assertDatabaseHas('roles', ['name' => 'Superuser']);
    }

    /** @test */
    public function it_assigns_super_user_to_user()
    {
        $this->assertFalse($this->test_user->can('superuser'));

        $this->artisan('seatplus:assign:superuser', ['characterName' => $this->test_user->characters->first()->name])
            ->expectsQuestion('Who should be superadmin?',$this->test_user->id)
            ->expectsQuestion('Do you wish to a continue?', 'y')
            ->assertExitCode(0);

        $this->assertTrue($this->test_user->refresh()->can('superuser'));
    }

    /** @test */
    public function a_second_super_user_can_not_be_assigned()
    {
        //1. give test user superuser
        $role = Role::findOrCreate('Superuser');
        $permission = Permission::findOrCreate('superuser');
        $role->givePermissionTo($permission);
        $this->test_user->assignRole($role);

        $secondary_user = Event::fakeFor(fn() => User::factory()->create()) ;

        $this->artisan('seatplus:assign:superuser', ['characterName' => $secondary_user->main_character])
            ->expectsOutput('Superuser has already been assigned, ask any of the following users to help you out:')
            ->assertExitCode(0);

        $this->assertTrue($this->test_user->refresh()->can('superuser'));
    }


}
