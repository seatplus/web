<?php

use Illuminate\Http\Request;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Web\Http\Middleware\SetLocale;
use Seatplus\Web\Support\Locales;
use Seatplus\Web\Support\Translations;
use Symfony\Component\HttpFoundation\Response;

/**
 * Run the SetLocale middleware for a synthetic request and return the resulting app locale.
 */
function localeFor(array $server = [], ?string $sessionLocale = null): string
{
    app()->setLocale(config('app.locale'));

    $request = Request::create('/', 'GET', server: $server);

    $session = app('session')->driver();
    if ($sessionLocale !== null) {
        $session->put('locale', $sessionLocale);
    }
    $request->setLaravelSession($session);
    app()->instance('request', $request);

    (new SetLocale)->handle($request, fn () => new Response);

    return app()->getLocale();
}

/*
 * SetLocale middleware — resolution chain (browser + session tiers, no column needed).
 */

test('resolves the locale from the browser Accept-Language header', function () {
    expect(localeFor(['HTTP_ACCEPT_LANGUAGE' => 'de']))->toBe('de');
});

test('ignores an unsupported browser locale and uses the app default', function () {
    expect(localeFor(['HTTP_ACCEPT_LANGUAGE' => 'fr']))->toBe('en');
});

test('defaults to the app locale with no signal', function () {
    expect(localeFor())->toBe('en');
});

test('available locales are derived from the lang directories', function () {
    expect(Locales::available())->toContain('en')->toContain('de');
});

test('available locales include locales from other package namespaces', function () {
    // A plugin (e.g. notifications) shipping only French under its own namespace.
    $dir = sys_get_temp_dir().'/i18n-'.uniqid();
    mkdir($dir.'/fr', 0777, true);
    app('translator')->addNamespace('discord', $dir);

    $available = Locales::available();

    rmdir($dir.'/fr');
    rmdir($dir);

    expect($available)->toContain('fr');
});

test('the session pick beats the browser language', function () {
    expect(localeFor(['HTTP_ACCEPT_LANGUAGE' => 'de'], sessionLocale: 'en'))->toBe('en');
});

/*
 * Translations::gather — the prop builder (structure + fallback).
 */

test('gather returns the namespaced structure for a locale', function () {
    $bag = Translations::gather(['web::auth'], 'de');

    expect($bag)->toHaveKey('web::')
        ->and($bag['web::']['auth']['login_welcome'])
        ->toBe('Willkommen, bitte melde dich über EVE Online SSO an');
});

test('gather falls back to the fallback locale for untranslated groups', function () {
    // de/wallet_journal.php does not exist → falls back to en per key.
    $bag = Translations::gather(['web::wallet_journal'], 'de');

    expect($bag['web::']['wallet_journal'])->not->toBeEmpty()
        ->and($bag['web::']['wallet_journal']['bounty_prize'] ?? null)->not->toBeNull();
});

test('a page declares its own translation groups as a pageTranslations prop', function () {
    test()->get(route('login'))
        ->assertInertia(fn (Assert $page) => $page->has('pageTranslations.web::.auth.login_welcome'));
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
 * Account column tier — needs the released auth users.locale column (CI resolves auth 4.1.3;
 * a stale local web vendor pinned to 4.1.1 will fail these until updated).
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

    expect(localeFor(['HTTP_ACCEPT_LANGUAGE' => 'en']))->toBe('de');
});
