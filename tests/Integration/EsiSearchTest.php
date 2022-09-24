<?php

use Seatplus\Eveapi\Models\Universe\System;
use Seatplus\Eveapi\Services\Facade\RetrieveEsiData;
use Inertia\Testing\AssertableInertia;

// First as vanilla user without necessairy 'esi-search.search_structures.v1' scope
// try getting the token and fail to do so

it('returns falsy if the user does not have the necessary scope', function () {
    $result = $this->actingAs($this->test_user)
        ->get(route('autosuggestion.token'))
        ->assertOk();

    expect($result->original)->toBeFalsy();
});

// without a token, try to do a search and fail to do so
it('throws an exception if the user does not have the necessary scope', function () {

    $this->actingAs($this->test_user)
        ->withoutExceptionHandling()
        ->get(route('autosuggestion.search', ['search' => 'jit', 'categories' => ['system']]))
        ->assertStatus(500);

})->throws('No ESI Search Token found, at least one character needs to have the scope esi-search.search_structures.v1');

// navigate to Enabling ESI Search page to create a token
it('can navigate to the enabling ESI Search page', function () {

    test()->actingAs($this->test_user)
        ->get(route('enable_esi_search'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('EnableEsiSearch')
                ->has('characters')
        );

    // pretent that the token has been created
    updateRefreshTokenWithScopes($this->test_character->refresh_token, ['esi-search.search_structures.v1']);

    // now if we return to the page, we should get redirected
    test()->actingAs($this->test_user)
        ->get(route('enable_esi_search'))
        ->assertRedirect('/');

});

// try get the token and succeed to do so
it('returns truthy if the user has the necessary scope', function () {
    updateRefreshTokenWithScopes($this->test_character->refresh_token, ['esi-search.search_structures.v1']);

    $result = $this->actingAs($this->test_user)
        ->get(route('autosuggestion.token'))
        ->assertOk();

    expect($result->original)->toBeTruthy();
});

// with a token try to do a search and succeed
it('can search existing system', function () {
    updateRefreshTokenWithScopes($this->test_character->refresh_token, ['esi-search.search_structures.v1']);

    $system = System::factory()->create([
        'name' => 'jita',
    ]);

    RetrieveEsiData::shouldReceive('execute')
        ->twice()
        ->andReturn(
            test()->mockEsiResponse([
                'solar_system' => [
                    $system->system_id,
                ],
            ]),
            test()->mockEsiResponse([
                [
                    'id' => $system->system_id,
                    'name' => $system->name,
                    'category' => 'solar_system',
                ],
            ])
        );

    $result = test()->actingAs(test()->test_user)
        ->get(route('autosuggestion.search', ['search' => 'jit', 'categories' => ['system']]))
        ->assertOk();


    expect($result->original)->toHaveCount(1);
});
