<?php

/*
 * Pest 4 browser smoke tests — load real pages in a headless browser, assert they
 * render without JS/console/page errors, and capture full-page screenshots (also
 * usable as documentation images). Tagged `browser` so they're excluded from the
 * normal unit/feature suite (they need built assets + Playwright + a served app).
 *
 * This is the foundation "proof" set (login + dashboard). The full per-domain
 * smoke suite + curated doc screenshots land in the dedicated browser PR.
 */

it('renders the login page without errors', function () {
    $page = visit('/login');

    $page->assertNoSmoke();
    $page->screenshot(true, 'login');
});

it('renders the dashboard for an authenticated user', function () {
    assignPermissionToTestUser('superuser');

    $this->actingAs($this->test_user);

    $page = visit(route('home'));

    $page->assertNoSmoke();
    $page->screenshot(true, 'dashboard');
});
