<?php


namespace Seatplus\Web\Tests;

use ClaudioDekker\Inertia\InertiaTestingServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Inertia\Inertia;
use Laravel\Horizon\HorizonServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Seatplus\Auth\AuthenticationServiceProvider;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\Eveapi\EveapiServiceProvider;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Http\Middleware\Authenticate;
use Seatplus\Web\Http\Middleware\HandleInertiaRequests;
use Seatplus\Web\Tests\Stubs\Kernel;
use Seatplus\Web\WebServiceProvider;
use Spatie\Permission\PermissionRegistrar;

abstract class TestCase extends OrchestraTestCase
{
    protected User $test_user;

    protected $test_character;

    protected function setUp(): void
    {

        parent::setUp();

        //Setup Inertia Root View
        Inertia::setRootView('web::app');

        //Do not use the queue
        Queue::fake();

        // setup database
        $this->setupDatabase($this->app);

        // setup factories
        $this->withFactories(__DIR__ . '/database/factories');


        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->test_user = Event::fakeFor(function () {
            return factory(User::class)->create();
        });

        $this->test_character = $this->test_user->characters->first();

        $this->app->instance('path.public', __DIR__ .'/../src/public');

        Permission::findOrCreate('superuser');
    }

    /**
     * Resolve application HTTP Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Http\Kernel', Kernel::class);
    }

    /**
     * Get application providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            WebServiceProvider::class,
            EveapiServiceProvider::class,
            HorizonServiceProvider::class,
            AuthenticationServiceProvider::class,
            InertiaTestingServiceProvider::class
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application  $app
     */
    private function setupDatabase($app)
    {
        // Path to our migrations to load
        //$this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->artisan('migrate', ['--database' => 'testbench']);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Use memory SQLite, cleans it self up
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        config(['app.debug' => true]);

        $app['router']->aliasMiddleware('auth', Authenticate::class);

        // Use test User model for users provider
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('cache.prefix', 'seatplus_tests---');

        //Setup Inertia for package development
        config()->set('inertia.page.paths', array_merge(
            config()->get('inertia.page.paths', []),
            [realpath(__DIR__ . '/../src/resources/js/Pages'), realpath(__DIR__ . '/../src/resources/js/Shared')],
        ));
    }

    protected function givePermissionsToTestUser(array $array)
    {

        foreach ($array as $string) {
            $permission = Permission::findOrCreate($string);

            $this->test_user->givePermissionTo($permission);
        }

        // now re-register all the roles and permissions
        $this->app->make(PermissionRegistrar::class)->registerPermissions();
    }

}
