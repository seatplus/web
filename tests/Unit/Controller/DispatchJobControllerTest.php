<?php


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
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

uses(TestCase::class);

beforeEach(function () {
    test()->dispatch_transfer_object = [
        'manual_job' => array_search(ContactHydrateBatch::class, config('web.jobs')),
        'permission' => config('eveapi.permissions.' . Contact::class),
        'required_scopes' => config('eveapi.scopes.character.contacts'),
        'required_corporation_role' => ''
    ];

});

it('dispatches job', function () {
    $mock = Mockery::mock('overload:' . ManualDispatchedJob::class);
    $mock->shouldReceive('handle')->andReturn(1);
    $mock->shouldReceive('setName')->andReturn($mock);
    $mock->shouldReceive('setJobs')->andReturn($mock);


    $dispatch_transfer_object = test()->dispatch_transfer_object;

    $character_id = test()->test_character->character_id;

    $cache_key = sprintf('%s:%s', $dispatch_transfer_object['manual_job'], $character_id);

    test()->assertNull(cache($cache_key));

    $response = test()->actingAs(test()->test_user)
        ->post(route('dispatch.job'),[
            'character_id' => $character_id,
            'dispatch_transfer_object' => $dispatch_transfer_object
        ]);

    test()->assertNotNull(cache($cache_key));
});

test('one get dispatchable entities', function () {

    $refresh_token = test()->test_character->refresh_token;
    $refresh_token->scopes = test()->dispatch_transfer_object['required_scopes'];
    $refresh_token->save();

    $response = test()->actingAs(test()->test_user)
        ->postJson(route('manual_job.entities'), test()->dispatch_transfer_object);

    $response->assertStatus(200)
        ->assertJson([
            [
                'character_id' => test()->test_character->character_id,
                'name' => test()->test_character->name,
                'batch' => ['state' =>'ready']
            ]
        ]);
});
