<?php


namespace Seatplus\Web\Tests;

use Laravel\Horizon\HorizonServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Seatplus\Eveapi\EveapiServiceProvider;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Web\Models\CharacterUser;
use Seatplus\Web\Models\User;
use Seatplus\Web\Tests\Stubs\Kernel;
use Seatplus\Web\WebServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected $test_user;

    protected $test_character;

    protected function setUp(): void
    {

        parent::setUp();

        // setup database
        $this->setupDatabase($this->app);

        // setup factories
        $this->withFactories(__DIR__ . '/database/factories');

        $this->test_user = factory(User::class)->create();

        $character_user = factory(CharacterUser::class)->make([
            'user_id' => $this->test_user->id,
            'character_id' => $this->test_user->id,
        ]);

        $this->test_user->characters()->save($character_user);

        $this->test_user->characters()->createMany(
            factory(CharacterUser::class, 3)->create()->toArray()
        );

        $this->test_character = factory(CharacterInfo::class)->create([
            'character_id' => $this->test_user->characters->first()->character_id,
            'name' => $this->test_user->characters->first()->user->name
        ]);
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
    }

    /**
     * Create the test response instance from the given response.
     *
     * @param  \Illuminate\Http\Response $response
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function createTestResponse($response)
    {
        return TestResponse::fromBaseResponse($response);
    }

}
