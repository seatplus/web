<?php


namespace Seatplus\Web;


use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\SocialiteManager;
use Seatplus\Web\Extentions\EveOnlineProvider;
use Seatplus\Web\Http\Middleware\Authenticate;
use Seatplus\Web\Http\Middleware\Locale;

class WebServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the JS & CSS,
        $this->addPublications();

        // Add routes
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');

        // Add views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'web');

        //Add Migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations/');

        // Add translations
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'web');

        // Add Middlewares
        $this->addMiddleware();
    }

    public function register()
    {
        // Register any extra services
        $this->register_services();
    }

    private function addPublications()
    {
        /*
         * to publish assets one can run:
         * php artisan vendor:publish --tag=web --force
         * or use Laravel Mix to copy the folder to public repo of core.
         */
        $this->publishes([
            __DIR__ . '/public/js' => public_path('js'),
            __DIR__ . '/public/css' => public_path('css'),
            __DIR__ . '/public/fonts' => public_path('fonts'),
            __DIR__ . '/public/img' => public_path('img'),
        ], 'web');
    }

    private function addMiddleware()
    {
        $router = $this->app['router'];

        /*
         * Authenticate checks that the session is authenticated,
         * if it is not, user is redirected to login-page
         */
        $router->aliasMiddleware('auth', Authenticate::class);

        /*
         * Localization support
         */
        $router->aliasMiddleware('locale', Locale::class);


    }

    private function register_services()
    {
        // Register the Socialite Factory.
        // From: Laravel\Socialite\SocialiteServiceProvider
        $this->app->singleton('Laravel\Socialite\Contracts\Factory', function ($app) {

            return new SocialiteManager($app);
        });

        // Slap in the Eveonline Socialite Provider
        $eveonline = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $eveonline->extend('eveonline',
            function ($app) use ($eveonline) {

                $config = $app['config']['services.eveonline'];

                return $eveonline->buildProvider(EveOnlineProvider::class, $config);
            }
        );
    }

}