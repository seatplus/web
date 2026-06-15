<?php

namespace Seatplus\Web\Tests\Browser;

use Illuminate\Foundation\Vite;
use Seatplus\Web\Tests\TestCase;

/**
 * Base test case for Pest 4 browser smoke tests.
 *
 * Reuses the package's full test environment (providers, test_user, Inertia config)
 * from the unit TestCase, but undoes its asset fakes: browser tests render real
 * pages driven by Playwright, so they need the real built Vite assets in public/
 * and the real Vite manifest (not withoutVite()'s fake).
 */
abstract class BrowserTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Serve real built assets from the package's public/ dir (parent points
        // path.public at tests/Stubs for unit tests).
        $this->app->instance('path.public', realpath(__DIR__.'/../../public'));

        // Pages must physically exist for a real browser render.
        config(['inertia.testing.ensure_pages_exist' => true]);

        // Parent calls withoutVite() (binds a fake Vite); drop it so @vite resolves
        // the real public/build/manifest.json and the Vue app actually mounts.
        $this->app->forgetInstance(Vite::class);
    }
}
