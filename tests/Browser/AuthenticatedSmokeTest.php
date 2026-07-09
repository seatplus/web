<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Models\Onboarding;

/*
 * Authenticated dashboard smoke test — runs against the real assembled core app.
 *
 * Logs in a factory user and loads /home, asserting the actual Dashboard renders
 * (PageHeader "Home") with no JS/console errors and styled — not just that some
 * page came back. In testing CheckRequiredScopes is skipped (non-production) and
 * ONBOARDING defaults off, so /home reaches Dashboard/Index; the Onboarding
 * record below keeps it passing even if onboarding is enabled.
 *
 * Factory-name guessing for the seatplus packages is set for the whole Browser
 * suite in core's tests/Pest.php.
 */

uses(RefreshDatabase::class);

it('renders the dashboard for an authenticated user', function () {
    $user = User::factory()->create();

    Onboarding::create(['user_id' => $user->getKey()]);

    $this->actingAs($user);

    $page = visit('/home');

    $page->assertNoSmoke();
    // "Characters" is the visible <h3> on the dashboard (Dashboard/Characters.vue);
    // "Home" is only the document <title>, so assert on real page content.
    $page->assertSee('Characters');
    $page->screenshot(true, 'dashboard');

    $this->assertAuthenticated();
});
