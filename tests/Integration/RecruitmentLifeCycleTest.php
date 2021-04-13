<?php


namespace Seatplus\Web\Tests\Integration;


use Inertia\Testing\Assert;
use Illuminate\Support\Facades\Event;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\Permissions\Role;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Application;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Universe\Region;
use Seatplus\Eveapi\Models\Universe\System;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class RecruitmentLifeCycleTest extends TestCase
{
    private User $secondary_user;
    private CharacterInfo $secondary_character;
    private User $superuser;

    public function setUp(): void
    {
        parent::setUp();

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->secondary_user = Event::fakeFor(function () {
            return User::factory()->create();
        });

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->superuser = Event::fakeFor(function () {
            $user = User::factory()->create();

            $permission = Permission::findOrCreate('superuser');

            $user->givePermissionTo($permission);

            // now re-register all the roles and permissions
            $this->app->make(PermissionRegistrar::class)->registerPermissions();

            return $user;
        });

        $this->secondary_character = $this->secondary_user->characters->first();
    }

    /** @test */
    public function user_without_permission_fails_to_create_enlistment()
    {

        $response = $this->actingAs($this->test_user)
            ->post(route('create.corporation.recruitment'), [
                'corporation_id' => $this->secondary_character->corporation->corporation_id,
                'type' => 'user'
            ])->assertForbidden();
    }

    /** @test */
    public function user_with_permission_and_affiliations_succeeds_to_create_enlistment()
    {

        $this->createEnlistment();
    }

    /** @test */
    public function user_with_permission_and_affiliations_can_delete_enlistment()
    {

        $this->createEnlistment();

        $this->assertDatabaseHas('enlistments',[
            'corporation_id' => $this->test_character->corporation->corporation_id,
        ]);

        $this->actingAs($this->test_user)
            ->delete(route('delete.corporation.recruitment', ['corporation_id' =>  $this->test_character->corporation->corporation_id]));

        $this->assertDatabaseMissing('enlistments',[
            'corporation_id' => $this->test_character->corporation->corporation_id,
        ]);
    }

    /** @test */
    public function secondary_user_can_see_enlistment()
    {

        $this->createEnlistment();

        $response = $this->actingAs($this->secondary_user)
            ->get(route('list.open.enlistments'))
            ->assertJson([
                [
                    'corporation_id' => $this->test_character->corporation->corporation_id
                ]
            ]);
    }

    /** @test */
    public function secondary_user_can_apply_as_character()
    {

        $this->createEnlistment('character');

        $this->assertNull($this->secondary_character->refresh()->application);

        $response = $this->actingAs($this->secondary_user)
            ->post(route('post.application'), [
                'corporation_id' => $this->test_character->corporation->corporation_id,
                'character_id' => $this->secondary_character->character_id
            ]);

        $this->assertNotNull($this->secondary_character->refresh()->application);
        $this->assertTrue($this->secondary_character->refresh()->application instanceof Application);

        // Pull application
        $response = $this->actingAs($this->secondary_user)
            ->delete(route('delete.character.application', $this->secondary_character->character_id));

        $this->assertNull($this->secondary_character->refresh()->application);
    }

    /** @test */
    public function secondary_user_can_apply_as_user()
    {

        $this->createEnlistment('user');

        $this->assertNull($this->secondary_user->refresh()->application);

        $this->applySecondary();

        $this->assertNotNull($this->secondary_user->refresh()->application);
        $this->assertTrue($this->secondary_user->refresh()->application instanceof Application);

        // pull application
        $response = $this->actingAs($this->secondary_user)
            ->delete(route('delete.user.application'));

        $this->assertNull($this->secondary_user->refresh()->application);
    }

    /** @test */
    public function senior_hr_sees_recruitment_component()
    {

        $this->assertFalse($this->test_user->can('superuser'));

        $response = $this->actingAs($this->test_user)
            ->get(route('corporation.recruitment'))
            ->assertForbidden();

        $this->givePermissionsToTestUser(['can open or close corporations for recruitment']);

        $this->actingAs($this->test_user->refresh())
            ->get(route('corporation.recruitment'))
            ->assertOk()
            ->assertInertia( fn (Assert $page) => $page->component('Corporation/Recruitment/RecruitmentIndex'));
    }

    /** @test */
    public function junior_hr_sees_recruitment_component()
    {
        $response = $this->actingAs($this->test_user)
            ->get(route('corporation.recruitment'))
            ->assertForbidden();

        $this->givePermissionsToTestUser(['can accept or deny applications']);

        $response = $this->actingAs($this->test_user->refresh())
            ->get(route('corporation.recruitment'))
            ->assertOk();

        $response->assertInertia( fn (Assert $page) => $page->component('Corporation/Recruitment/RecruitmentIndex'));
    }

    /** @test */
    public function junior_hr_handles_open_user_applications()
    {
        $this->createEnlistment();

        $this->test_user = $this->test_user->refresh();

        $this->actingAs($this->test_user)
            ->get(route('open.corporation.applications', $this->test_character->corporation->corporation_id))
            ->assertJsonCount(0, 'data');

        $this->applySecondary();

        $this->actingAs($this->test_user)
            ->get(route('open.corporation.applications', $this->test_character->corporation->corporation_id))
            ->assertJsonCount(1, 'data');

        // open application

        $response = $this->actingAs($this->test_user)
            ->get(route('user.application', ['recruit' => $this->secondary_user->id]))
            ->assertOk()
            ->assertInertia( fn (Assert $page) => $page->component('Corporation/Recruitment/Application'));


        // Impersonate

        $this->actingAs($this->test_user)
            ->get(route('impersonate.recruit', ['recruit' => $this->secondary_user->id]))
            ->assertRedirect(route('home'))
            ->assertSessionHas('impersonation_origin', $this->test_user);

        // Stop Impersonate

        $this->actingAs($this->secondary_user)
            ->withSession(['impersonation_origin' => $this->test_user, 'route' => route('home')])
            ->get(route('impersonate.stop'))
            ->assertRedirect(route('home'))
            ->assertSessionMissing(['impersonation_origin', 'route']);

        // submit review

        $this->assertDatabaseHas('applications', [
            'applicationable_id' => $this->secondary_user->id,
            'applicationable_type' => User::class,
            'status' => 'open'
        ]);

        $this->actingAs($this->test_user)
            ->post(route('review.user.application', ['recruit' => $this->secondary_user->id]), [
                'decision' => 'rejected',
                'explanation' => 'Some reason'
            ])
            ->assertRedirect(route('corporation.recruitment'));

        $this->assertDatabaseHas('applications', [
            'applicationable_id' => $this->secondary_user->id,
            'applicationable_type' => User::class,
            'status' => 'rejected'
        ]);

        $this->assertNull($this->secondary_user->refresh()->application);

    }

    /** @test */
    public function junior_hr_handles_open_character_applications()
    {
        $this->createEnlistment();

        $this->test_user = $this->test_user->refresh();

        $this->actingAs($this->test_user)
            ->get(route('open.corporation.applications', $this->test_character->corporation->corporation_id))
            ->assertJsonCount(0, 'data');

        $this->applySecondary(false);

        $response = $this->actingAs($this->test_user)
            ->get(route('open.corporation.applications', $this->test_character->corporation->corporation_id))
            ->assertJsonCount(1, 'data');

        // open application

        $response = $this->actingAs($this->test_user)
            ->get(route('character.application', ['character_id' => $this->secondary_character->character_id]))
            ->assertOk()
            ->assertInertia( fn (Assert $page) => $page->component('Corporation/Recruitment/Application'));

        // submit review

        $this->assertDatabaseHas('applications', [
            'applicationable_id' => $this->secondary_character->character_id,
            'applicationable_type' => CharacterInfo::class,
            'status' => 'open'
        ]);

        $this->actingAs($this->test_user)
            ->post(route('review.character.application', ['character_id' => $this->secondary_character->character_id]), [
                'decision' => 'rejected',
                'explanation' => 'Some reason'
            ])
            ->assertRedirect(route('corporation.recruitment'));

        $this->assertDatabaseHas('applications', [
            'applicationable_id' => $this->secondary_character->character_id,
            'applicationable_type' => CharacterInfo::class,
            'status' => 'rejected'
        ]);

        $this->assertNull($this->secondary_character->refresh()->application);

    }

    /** @test */
    public function juniorHR_can_see_shitlist()
    {
        $this->createEnlistment();

        $this->test_user = $this->test_user->refresh();

        // Create SSO Setting

        // Give Test User required scope

        // Test that test user is not on shitlist
        $this->actingAs($this->test_user)
            ->get(route('open.corporation.applications', $this->test_character->corporation->corporation_id))
            ->assertJsonCount(0, 'data');

        $this->applySecondary();

        $this->actingAs($this->test_user)
            ->get(route('open.corporation.applications', $this->test_character->corporation->corporation_id))
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function seniorHR_can_setup_watchlist()
    {
        $this->createEnlistment();

        $this->actingAs($this->test_user->refresh())
            ->get(route('get.watchlist', $this->test_character->corporation->corporation_id))
            ->assertOk()
            ->assertInertia( fn (Assert $page) => $page
                ->component('Corporation/Recruitment/Watchlist/Index')
                ->has('watched_systems', 0)
                ->has('watched_regions', 0)
            );

        // create system
        $system =  System::factory()->create();

        // watchlist system
        $this->actingAs($this->test_user->refresh())
            ->followingRedirects()
            ->post(route('update.watchlist', $this->test_character->corporation->corporation_id), [
                'systems' => [
                    (object) [
                        'id' => $system->system_id
                    ]
                ]
            ])
            ->assertInertia( fn (Assert $page) => $page
                ->component('Corporation/Recruitment/Watchlist/Index')
                ->has('watched_systems', 1, fn(Assert $page) => $page
                    ->where('id', $system->system_id)
                    ->etc()
                )
                ->has('watched_regions', 0)
            );

        // add region
        $region = Region::factory()->create();
        $this->actingAs($this->test_user->refresh())
            ->followingRedirects()
            ->post(route('update.watchlist', $this->test_character->corporation->corporation_id), [
                'systems' => [
                    (object) [
                        'id' => $system->system_id
                    ]
                ],
                'regions' => [
                    (object) [
                        'id' => $region->region_id
                    ]
                ]
            ])
            ->assertInertia( fn (Assert $page) => $page
                ->component('Corporation/Recruitment/Watchlist/Index')
                ->has('watched_systems', 1, fn(Assert $page) => $page
                    ->where('id', $system->system_id)
                    ->etc()
                )
                ->has('watched_regions', 1, fn(Assert $page) => $page
                    ->where('id', $region->region_id)
                    ->etc()
                )
            );

    }

    /** @test */
    public function RecruiterCanSeeCorporationApplications()
    {
        // Create Enlistment
        $this->createEnlistment('character');

        // create Senior Recruiter user

        $recruiter = Event::fakeFor(fn() =>User::factory()->create());

        // give user roles

        $role = Role::findByName('test');

        $response = $this->actingAs($this->superuser)
            ->followingRedirects()
            ->json('POST', route('update.acl.affiliations', ['role_id' => $role->id]), [
                "acl" => [
                    "type" => 'manual',
                    'affiliations' => [],
                    'members' => [
                        [
                            'id' => $recruiter->id,
                            'user' => $recruiter
                        ],
                    ]
                ]
            ])->assertOk();

        $this->assertTrue($recruiter->refresh()->hasRole($role));

        // get list with open appliactions
        $response = $this->actingAs($recruiter)
            ->get(route('open.corporation.applications', $this->test_character->corporation->corporation_id))
            ->assertOk();

        // Apply with secondary user
        $this->applySecondary(false);

        // Get the test_users Applicaton // /character_application/{character_id}
        $response = $this->actingAs($recruiter)
            ->get(route('character.application', $this->secondary_character->character_id))
            ->assertOk();

        // Get the test_users wallet journal as example that a recruiter does get permissions to any other recruit specific endpoint
        $this->actingAs($recruiter)
            ->get(route('character.wallet_journal.detail', $this->secondary_character->character_id))
            ->assertOk();

        // Any other character should be forbidden
        $this->actingAs($recruiter)
            ->get(route('character.wallet_journal.detail', $this->secondary_character->character_id+1))
            ->assertForbidden();

    }

    private function applySecondary(bool $user = true)
    {

        $payload = $user
            ? ['corporation_id' => $this->test_character->corporation->corporation_id]
            : ['corporation_id' => $this->test_character->corporation->corporation_id, 'character_id' => $this->secondary_character->character_id];

        $this->actingAs($this->secondary_user)
            ->post(route('post.application'), $payload);
    }

    private function createEnlistment($type = 'user',string $affiliation = 'allowed')
    {
        // create role
        $this->actingAs($this->superuser)
            ->followingRedirects()
            ->json('POST', route('acl.create'), ['name' => 'test']);

        // affiliate secondary user to role
        $role = Role::findByName('test');

        $response = $this->actingAs($this->superuser)
            ->json('POST', route('acl.update', ['role_id' => $role->id]), [
                "permissions" => ["can open or close corporations for recruitment", "can accept or deny applications"],
                $affiliation => [
                    [
                        "corporation_id" => $this->test_character->corporation->corporation_id,
                        "id" => $this->test_character->corporation->corporation_id,
                        "name" => $this->test_character->corporation->name
                    ],
                ],
            ]);

        // give test user the role

        $response = $this->actingAs($this->superuser)
            ->followingRedirects()
            ->json('POST', route('update.acl.affiliations', ['role_id' => $role->id]), [
                "acl" => [
                    "type" => 'manual',
                    'affiliations' => [],
                    'members' => [
                        [
                            'id' => $this->test_user->id,
                            'user' => $this->test_user
                        ],
                    ]
                ]
            ])->assertOk();

        $this->assertTrue($this->test_user->refresh()->hasRole($role));

        // Create Enlistment

        $this->assertDatabaseMissing('enlistments',[
            'corporation_id' => $this->test_character->corporation->corporation_id,
        ]);

        // Create Enlistment as test user
        $response = $this->actingAs($this->test_user)
            ->post(route('create.corporation.recruitment'), [
                'corporation_id' => $this->test_character->corporation->corporation_id,
                'type' => $type
            ]);

        $this->assertDatabaseHas('enlistments',[
            'corporation_id' => $this->test_character->corporation->corporation_id,
        ]);
    }



}
