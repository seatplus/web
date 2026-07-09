<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Auth\Database\Factories\UserFactory;

/*
 * Authenticated browser smoke test — runs against the real assembled core app.
 *
 * Logs in a factory user and hits an authed route. A fresh user has no SSO
 * scopes yet, so the auth + CheckRequiredScopes + Onboarding middleware chain
 * routes them through the onboarding/scopes flow rather than the dashboard —
 * which is exactly what we want to smoke: that an authenticated page renders
 * with no JS/console errors and styled. (A scoped-user dashboard test is a
 * follow-up once the scope/character setup is factored out.)
 *
 * We instantiate the auth UserFactory directly rather than User::factory(),
 * because core has no Testbench factory-name guessing.
 */
uses(RefreshDatabase::class);

it('renders an authenticated page without errors', function () {
    $user = UserFactory::new()->create();

    $this->actingAs($user);

    $page = visit('/home');

    $page->assertNoSmoke();
    $page->screenshot(true, 'authenticated');

    $this->assertAuthenticated();
});
