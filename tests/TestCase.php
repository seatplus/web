<?php


namespace Seatplus\Web\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
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
use Seatplus\Web\Http\Middleware\Authenticate;
use Seatplus\Web\Models\Asset\Asset;
use Seatplus\Web\Tests\Stubs\ConsoleKernel;
use Seatplus\Web\Tests\Stubs\Kernel;
use Seatplus\Web\Tests\Traits\MockRetrieveEsiDataAction;
use Seatplus\Web\WebServiceProvider;
use Staudenmeir\LaravelCte\DatabaseServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    use MockRetrieveEsiDataAction;
    use LazilyRefreshDatabase;

    public User $test_user;

    public CharacterInfo $test_character;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => match (true) {
                Str::startsWith($modelName, 'Seatplus\Auth') => 'Seatplus\\Auth\\Database\\Factories\\' . class_basename($modelName) . 'Factory',
                Str::startsWith($modelName, 'Seatplus\Eveapi'), Asset::class === $modelName => 'Seatplus\\Eveapi\\Database\\Factories\\' . class_basename($modelName) . 'Factory',
                Str::startsWith($modelName, 'Seatplus\Web') => 'Seatplus\\Web\\Database\\Factories\\' . class_basename($modelName) . 'Factory',
            }
        );

        //Setup Inertia Root View
        Inertia::setRootView('web::app');

        //Do not use the queue
        Queue::fake();

        $this->test_user = Event::fakeFor(fn () => User::factory()->create());

        $this->test_character = $this->test_user->characters->first();

        $this->app->instance('path.public', __DIR__ .'/Stubs');

        Permission::findOrCreate('superuser');

        $this->withoutVite();
    }

    /**
     * Resolve application Console Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton('Illuminate\Contracts\Console\Kernel', ConsoleKernel::class);
    }

    /**
     * Resolve application HTTP Kernel implementation.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function resolveApplicationHttpKernel($app)
    {
        $app->singleton(\Illuminate\Contracts\Http\Kernel::class, Kernel::class);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            WebServiceProvider::class,
            EveapiServiceProvider::class,
            DatabaseServiceProvider::class,
            HorizonServiceProvider::class,
            AuthenticationServiceProvider::class,
            InertiaServiceProviderAlias::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app) : void
    {

        config(['app.debug' => true]);

        $app['router']->aliasMiddleware('auth', Authenticate::class);

        // Use test User model for users provider
        $app['config']->set('auth.providers.users.model', User::class);

        $app['config']->set('cache.prefix', 'seatplus_tests---');

        //Setup Inertia for package development
        config()->set('inertia.testing.page_paths', array_merge(
            config()->get('inertia.testing.page_paths', []),
            [
                realpath(__DIR__ . '/../resources/js/Pages'),
                realpath(__DIR__ . '/../resources/js/Shared'),
            ],
        ));
    }
}
