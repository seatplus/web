<?php

namespace Seatplus\Web\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\ServiceProvider as InertiaServiceProviderAlias;
use Laravel\Horizon\HorizonServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Seatplus\Auth\AuthenticationServiceProvider;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\EveapiServiceProvider;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Character\CharacterRole;
use Seatplus\Web\Http\Middleware\Authenticate;
use Seatplus\Web\Tests\Stubs\ConsoleKernel;
use Seatplus\Web\Tests\Stubs\Kernel;
use Seatplus\Web\WebServiceProvider;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\PermissionServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    use LazilyRefreshDatabase;

    public User $test_user;

    public CharacterInfo $test_character;

    protected function setUp(): void
    {
        // Hard stop: never run the suite against the dev database. The phpunit
        // <env DB_DATABASE="laravel" force="true"> override has been observed to be bypassed
        // in the local dev container (the shell exports DB_DATABASE=seatplus), in which case
        // LazilyRefreshDatabase would migrate:fresh the dev DB and wipe it. Abort loudly
        // before parent::setUp() boots anything or a factory touches the connection.
        // Read every source env() consults rather than env() itself, which larastan
        // (rightly) flags outside config/: narrowing this to one source would weaken a
        // guard whose whole job is to be impossible to slip past.
        $resolved_databases = [$_ENV['DB_DATABASE'] ?? null, $_SERVER['DB_DATABASE'] ?? null, getenv('DB_DATABASE')];

        if (in_array('seatplus', $resolved_databases, true)) {
            throw new \RuntimeException('Test suite resolved DB_DATABASE=seatplus (the dev database) — aborting to avoid wiping dev data. The phpunit force override did not hold.');
        }

        parent::setUp();

        Model::shouldBeStrict();

        cache()->flush();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => match (true) {
                Str::startsWith($modelName, 'Seatplus\Auth') => 'Seatplus\\Auth\\Database\\Factories\\'.class_basename($modelName).'Factory',
                Str::startsWith($modelName, 'Seatplus\Eveapi') => 'Seatplus\\Eveapi\\Database\\Factories\\'.class_basename($modelName).'Factory',
                Str::startsWith($modelName, 'Seatplus\Web') => 'Seatplus\\Web\\Database\\Factories\\'.class_basename($modelName).'Factory',
                default => throw new \UnhandledMatchError(sprintf('No factory namespace is registered for %s.', $modelName)),
            }
        );

        // Setup Inertia Root View
        Inertia::setRootView('web::app');

        // Do not use the queue
        Queue::fake();

        $this->test_user = Event::fakeFor(fn () => User::factory()->create());

        $this->test_character = $this->test_user->characters->first();

        // Ensure the test character has no in-game EVE corporation roles by default.
        // CharacterInfoFactory assigns random roles (possibly Director) which bypasses
        // all permission checks in CanUserService. Tests that need specific roles set them explicitly.
        CharacterRole::updateOrCreate(
            ['character_id' => $this->test_character->character_id],
            ['roles' => [], 'roles_at_base' => null, 'roles_at_hq' => null, 'roles_at_other' => null]
        );

        $this->app->instance('path.public', __DIR__.'/Stubs');

        // Backend PR: Vue page files don't exist yet — skip physical file existence check.
        // Controller tests still assert the correct component name and props are returned.
        config(['inertia.testing.ensure_pages_exist' => false]);

        Permission::findOrCreate('superuser');

        $this->withoutVite();
    }

    /**
     * Resolve application Console Kernel implementation.
     *
     * @param  Application  $app
     * @return void
     */
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Console\Kernel', ConsoleKernel::class);
    }

    /**
     * Resolve application HTTP Kernel implementation.
     *
     * @param  Application  $app
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton(\Illuminate\Contracts\Http\Kernel::class, Kernel::class);
    }

    /**
     * Get package providers.
     *
     * @param  Application  $app
     * @return array<int, class-string<ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            AuthenticationServiceProvider::class,
            EveapiServiceProvider::class,
            HorizonServiceProvider::class,
            InertiaServiceProviderAlias::class,
            PermissionServiceProvider::class,
            WebServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        config(['app.debug' => true]);

        $app['router']->aliasMiddleware('auth', Authenticate::class);

        // Use test User model for users provider
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('cache.prefix', 'seatplus_tests---');

        // Setup Inertia for package development
        config()->set('inertia.pages.paths', array_merge(
            config()->get('inertia.pages.paths', []),
            [
                realpath(__DIR__.'/../resources/js/Pages'),
                realpath(__DIR__.'/../resources/js/Shared'),
            ],
        ));
    }
}
