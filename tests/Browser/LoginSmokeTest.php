<?php

/*
 * Browser smoke test for the login page — authored in the web package (where the
 * Vue pages live) but executed against a real, fully-assembled core app, never
 * under web's own Testbench harness (web's phpunit.xml excludes tests/Browser).
 *
 * It runs in two places, both driving the same assembled app:
 *   - core's browser CI, against the released web package, and
 *   - a web PR's browser job, which clones core and overlays this branch into
 *     packages/web so the PR's frontend is what renders.
 *
 * `visit()` / assertNoSmoke() / screenshot() come from pest-plugin-browser, which
 * is provided by the core app the tests run inside — not by the web package.
 */
it('renders the login page', function () {
    $page = visit('/login');

    $page->assertNoSmoke();
    $page->assertSee('Sign in');
    $page->screenshot(true, 'login');
});
