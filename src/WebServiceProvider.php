<?php


namespace Seatplus\Web;


use Illuminate\Support\ServiceProvider;

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
    }

    private function addPublications()
    {
        /*
         * to publish assets one can run:
         * php artisan vendor:publish --tag=web --force
         * or use Laravel Mix to copy the folder to public repo of core.
         */
        $this->publishes([
            __DIR__ . '/resources/js' => public_path(''),
            __DIR__ . '/resources/css' => public_path(''),
        ], 'web');
    }

}