<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Models\Onboarding;

/*
 * Authenticated dashboard smoke test — runs against the real assembled core app.
 *
 * Logs in a user and loads /home, asserting the Dashboard actually renders
 * (the visible "Characters" heading) with no JS/console errors — not just that
 * some page came back.
 *
 * The user is created minimally (no characters) on purpose: User::factory()
 * builds a multi-character graph whose CharacterInfo affiliations occasionally
 * collide on character_affiliations (random ids), which is flaky here. A
 * characterless user is deterministic and still reaches Dashboard/Index (an
 * empty character list). In testing CheckRequiredScopes is skipped
 * (non-production) and ONBOARDING defaults off; the Onboarding record keeps it
 * passing even if onboarding is enabled.
 */

uses(RefreshDatabase::class);

it('renders the dashboard for an authenticated user', function () {
    $user = new User();
    $user->active = true;
    $user->remember_token = Str::random(10);
    $user->save();

    Onboarding::create(['user_id' => $user->getKey()]);

    $this->actingAs($user);

    $page = visit('/home');

    // TEMP DIAGNOSTIC: raw page HTML to CI log (untruncated) to see what #app contains.
    fwrite(STDERR, "\n<<<PAGEHTML>>>\n".$page->content()."\n<<<ENDHTML>>>\n");

    $page->assertNoSmoke();
    $page->assertSee('Characters');
    $page->screenshot(true, 'dashboard');

    $this->assertAuthenticated();
});
