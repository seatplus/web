<?php


namespace Seatplus\Web\Tests\Unit\Controller;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Mockery;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Jobs\Hydrate\Character\ContactHydrateBatch;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Eveapi\Services\DispatchIndividualUpdate;
use Seatplus\Web\Jobs\ManualDispatchedJob;
use Seatplus\Web\Tests\TestCase;
use Spatie\Permission\PermissionRegistrar;

class DispatchJobControllerTest extends TestCase
{
    protected array $dispatch_transfer_object;

    public function setUp(): void
    {
        parent::setUp();

        $this->dispatch_transfer_object = [
            'manual_job' => array_search(ContactHydrateBatch::class, config('web.jobs')),
            'permission' => config('eveapi.permissions.' . Contact::class),
            'required_scopes' => config('eveapi.scopes.character.contacts'),
            'required_corporation_role' => ''
        ];

    }

    /** @test */
    public function it_dispatches_job()
    {
        $mock = Mockery::mock('overload:' . ManualDispatchedJob::class);
        $mock->shouldReceive('handle')->andReturn(1);
        $mock->shouldReceive('setName')->andReturn($mock);
        $mock->shouldReceive('setJobs')->andReturn($mock);


        $dispatch_transfer_object = $this->dispatch_transfer_object;

        $character_id = $this->test_character->character_id;

        $cache_key = sprintf('%s:%s', $dispatch_transfer_object['manual_job'], $character_id);

        $this->assertNull(cache($cache_key));

        $response = $this->actingAs($this->test_user)
            ->post(route('dispatch.job'),[
                'character_id' => $character_id,
                'dispatch_transfer_object' => $dispatch_transfer_object
            ]);

        $this->assertNotNull(cache($cache_key));
    }

    /** @test */
    public function one_get_dispatchable_entities()
    {

        $refresh_token = $this->test_character->refresh_token;
        $refresh_token->scopes = $this->dispatch_transfer_object['required_scopes'];
        $refresh_token->save();

        $response = $this->actingAs($this->test_user)
            ->postJson(route('manual_job.entities'), $this->dispatch_transfer_object);

        $response->assertStatus(200)
            ->assertJson([
                [
                    'character_id' => $this->test_character->character_id,
                    'name' => $this->test_character->name,
                    'batch' => 'ready'
                ]
            ]);
    }

}
