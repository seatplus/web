<?php

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
    updateRefreshTokenWithScopes($this->test_character->refreshToken, ['esi-search.search_structures.v1']);

    // now if we return to the page, we should get redirected
    test()->actingAs($this->test_user)
        ->get(route('enable_esi_search'))
        ->assertRedirect('/');
});

// the step-up upgrade link must carry the origin page so the user returns there, not the dashboard
it('passes the origin page as the step-up redirect target', function () {
    test()->actingAs($this->test_user)
        ->from('/acl/create')
        ->get(route('enable_esi_search'))
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('EnableEsiSearch')
                ->where('characters.0.upgrade_url', fn ($url) => str_contains(urldecode($url), '/acl/create'))
        );
});

// try get the token and succeed to do so
it('returns truthy if the user has the necessary scope', function () {
    updateRefreshTokenWithScopes($this->test_character->refreshToken, ['esi-search.search_structures.v1']);

    $result = $this->actingAs($this->test_user)
        ->get(route('autosuggestion.token'))
        ->assertOk();

    expect($result->original)->toBeTruthy();
});
