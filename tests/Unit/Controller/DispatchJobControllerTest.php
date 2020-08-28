<?php


namespace Seatplus\Web\Tests\Unit\Controller;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Mockery;
use Seatplus\Auth\Models\CharacterUser;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Services\DispatchIndividualUpdate;
use Seatplus\Web\Tests\TestCase;

class DispatchJobControllerTest extends TestCase
{
    /** @test */
    public function it_dispatches_job()
    {
        $mock = Mockery::mock('overload:' . DispatchIndividualUpdate::class);
        $mock->shouldReceive('execute')->andReturn(1);

        $job = Arr::first(array_keys(config('eveapi.jobs')));
        $character_id = $this->test_character->character_id;

        $cache_key = sprintf('%s:%s', $job, $character_id);

        $this->assertNull(cache($cache_key));

        $response = $this->actingAs($this->test_user)
            ->post(route('dispatch.job'),[
                'character_id' => $character_id,
                'job' => $job
            ]);

        $this->assertNotNull(cache($cache_key));
    }

}
