<?php

use Illuminate\Http\Request;
use Seatplus\Web\Http\Middleware\HandleInertiaRequests;

/**
 * Invoke the shared `locale` resolution for a synthetic request. The
 * HandleInertiaRequests middleware isn't wired into the Testbench HTTP stack
 * (controllers supply their own props in tests), so we exercise resolveLocale()
 * directly through the public share() API. Returns the resolved locale and, as a
 * side effect, sets the application locale.
 */
function sharedLocale(array $server = []): string
{
    $request = Request::create('/', 'GET', server: $server);

    return value(app(HandleInertiaRequests::class)->share($request)['locale']);
}

/*
 * Resolution chain — browser + session tiers (no users.locale column required).
 */

test('resolves the locale from the browser Accept-Language header', function () {
    expect(sharedLocale(['HTTP_ACCEPT_LANGUAGE' => 'de']))->toBe('de');
    expect(app()->getLocale())->toBe('de');
});

test('ignores an unsupported browser locale and uses the app default', function () {
    expect(sharedLocale(['HTTP_ACCEPT_LANGUAGE' => 'fr']))->toBe('en');
});

test('the session pick beats the browser language', function () {
    session(['locale' => 'en']);

    expect(sharedLocale(['HTTP_ACCEPT_LANGUAGE' => 'de']))->toBe('en');
});

test('defaults to the app locale with no signal', function () {
    expect(sharedLocale())->toBe('en');
});

/*
 * Switch route (LocaleController) — guest-allowed.
 */

test('a guest switch is stored in the session', function () {
    test()->post(route('locale.update'), ['locale' => 'de'])
        ->assertRedirect()
        ->assertSessionHas('locale', 'de');
});

test('an unsupported locale is rejected', function () {
    test()->post(route('locale.update'), ['locale' => 'klingon'])
        ->assertSessionHasErrors('locale');

    test()->assertNull(session('locale'));
});

test('the missing translation keys command runs', function () {
    test()->artisan('seatplus:i18n:missing-keys')->assertSuccessful();
});

/*
 * Account column tier — activates once the auth package ships users.locale
 * (seatplus/auth#413). Until then the column is absent and these fail.
 */

test('an authenticated choice persists to the account', function () {
    test()->actingAs(test()->test_user)
        ->post(route('locale.update'), ['locale' => 'de'])
        ->assertRedirect();

    expect(test()->test_user->refresh()->locale)->toBe('de');
});

test('the account locale wins over the browser language', function () {
    test()->test_user->update(['locale' => 'de']);
    test()->actingAs(test()->test_user);

    expect(sharedLocale(['HTTP_ACCEPT_LANGUAGE' => 'en']))->toBe('de');
});
