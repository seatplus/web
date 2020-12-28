<?php


namespace Seatplus\Web\Tests\Unit\Controller;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Mockery;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Jobs\Hydrate\Character\ContactHydrateBatch;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Contacts\Contact;
use Seatplus\Eveapi\Services\DispatchIndividualUpdate;
use Seatplus\Web\Jobs\ManualDispatchedJob;
use Seatplus\Web\Tests\TestCase;

class DispatchJobControllerTest extends TestCase
{
    /** @test */
    public function it_dispatches_job()
    {
        $mock = Mockery::mock('overload:' . ManualDispatchedJob::class);
        $mock->shouldReceive('handle')->andReturn(1);
        $mock->shouldReceive('setName')->andReturn($mock);
        $mock->shouldReceive('setJobs')->andReturn($mock);


        $dispatch_transfer_object = [
            'manual_job' => array_search(ContactHydrateBatch::class, config('web.jobs')),
            'permission' => config('eveapi.permissions.' . Contact::class),
            'required_scopes' => config('eveapi.scopes.character.contacts'),
            'required_corporation_role' => ''
        ];

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

}
