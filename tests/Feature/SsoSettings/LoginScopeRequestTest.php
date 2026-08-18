<?php

use Seatplus\Eveapi\Models\Corporation\CorporationInfo;
use Seatplus\Eveapi\Models\SsoScopes;

/*
 * The installation-wide requirement is the only scope list EVE is asked for at sign-in
 * (RedirectSSOController::getScopes() = eveapi.scopes.minimum + the type='global' rows). Because
 * GlobalSsoScopesService::get() plucks an array column, it returns one nested array per row, and
 * Socialite's formatScopes() implodes the list — so an unflattened value raised "Array to string
 * conversion" and 500'd sign-in and add-character. WebServiceProvider binds a flattening subclass;
 * these pin that the redirect carries a flat, space-separated scope string.
 */

function ssoRedirectScopes(string $location): array
{
    parse_str((string) parse_url($location, PHP_URL_QUERY), $query);

    return explode(' ', $query['scope'] ?? '');
}

it('requests only the minimum scopes when nothing is configured installation-wide', function () {
    // The controller stashes the previous URL to return to, which Session::previousUrl() types as a
    // non-nullable string — so give the session one.
    $response = test()->actingAs(test()->test_user)->from('/')->get(route('auth.eve'));

    $response->assertRedirect();

    expect(ssoRedirectScopes($response->headers->get('Location')))->toBe(['publicData']);
});

it('requests the installation-wide scopes as a flat list', function () {
    SsoScopes::factory()->create([
        'morphable_id' => null,
        'morphable_type' => null,
        'type' => 'global',
        'selected_scopes' => ['esi-assets.read_assets.v1', 'esi-skills.read_skills.v1'],
    ]);

    // The controller stashes the previous URL to return to, which Session::previousUrl() types as a
    // non-nullable string — so give the session one.
    $response = test()->actingAs(test()->test_user)->from('/')->get(route('auth.eve'));

    $response->assertRedirect();

    // The failure mode this guards is a literal "Array" in the scope string (or an ErrorException
    // before the redirect is even built), not a missing scope.
    expect(ssoRedirectScopes($response->headers->get('Location')))
        ->toContain('publicData')
        ->toContain('esi-assets.read_assets.v1')
        ->toContain('esi-skills.read_skills.v1')
        ->not->toContain('Array');
});

it('does not request a corporation requirement at sign-in', function () {
    $corporation = CorporationInfo::factory()->create();

    SsoScopes::factory()->create([
        'morphable_id' => $corporation->corporation_id,
        'morphable_type' => CorporationInfo::class,
        'type' => 'default',
        'selected_scopes' => ['esi-mail.read_mail.v1'],
    ]);

    // The controller stashes the previous URL to return to, which Session::previousUrl() types as a
    // non-nullable string — so give the session one.
    $response = test()->actingAs(test()->test_user)->from('/')->get(route('auth.eve'));

    $response->assertRedirect();

    expect(ssoRedirectScopes($response->headers->get('Location')))->toBe(['publicData']);
});
