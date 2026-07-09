<?php

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Seatplus\Auth\Models\User;

/*
 * Authenticated browser smoke test — runs against the real assembled core app.
 *
 * Logs in a factory user and hits an authed route. A fresh user has no SSO
 * scopes, so the auth + CheckRequiredScopes + Onboarding middleware chain routes
 * them through the onboarding/scopes flow rather than the dashboard — which is
 * exactly what we smoke: that an authenticated page renders with no JS/console
 * errors and styled. (A scoped-user dashboard test is a follow-up.)
 */

uses(RefreshDatabase::class);

/*
 * Core (a plain Laravel app) has none of the web package's Testbench factory-name
 * guessing, so User::factory() and the related factories it invokes (CharacterInfo,
 * …) can't be resolved by default. Map the seatplus model namespaces to their
 * package factory namespaces, mirroring the web TestCase.
 */
beforeEach(function () {
    Factory::guessFactoryNamesUsing(fn (string $model) => match (true) {
        Str::startsWith($model, 'Seatplus\\Auth') => 'Seatplus\\Auth\\Database\\Factories\\'.class_basename($model).'Factory',
        Str::startsWith($model, 'Seatplus\\Eveapi') => 'Seatplus\\Eveapi\\Database\\Factories\\'.class_basename($model).'Factory',
        Str::startsWith($model, 'Seatplus\\Web') => 'Seatplus\\Web\\Database\\Factories\\'.class_basename($model).'Factory',
        default => 'Database\\Factories\\'.class_basename($model).'Factory',
    });
});

it('renders an authenticated page without errors', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit('/home');

    $page->assertNoSmoke();
    $page->screenshot(true, 'authenticated');

    $this->assertAuthenticated();
});
