<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Seatplus\Web;

use Illuminate\Support\ServiceProvider;
use Inertia\ExceptionResponse;
use Inertia\Inertia;
use Seatplus\Web\Console\Commands\AssignSuperuser;
use Seatplus\Web\Contracts\WebJobsRepository;
use Seatplus\Web\Http\Middleware\Authenticate;
use Seatplus\Web\Http\Middleware\HandleInertiaRequests;
use Seatplus\Web\Http\Middleware\Locale;
use Spatie\Permission\Middleware\PermissionMiddleware;

class WebServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function boot(): void
    {
        // Publish the JS & CSS,
        $this->addPublications();

        // Add routes
        $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');

        // Add views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'web');

        // Add Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/');

        // Add translations
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'web');

        // Add Middlewares
        $this->addMiddleware();

        // Add commands
        $this->addCommands();

        // Register Inertia error page handler.
        // In local dev we skip 500/503 so Laravel's Ignition debug page is shown instead.
        Inertia::handleExceptionsUsing(function (ExceptionResponse $response) {
            $codes = app()->isLocal()
                ? [403, 404]
                : [403, 404, 500, 503];

            if (in_array($response->statusCode(), $codes)) {
                return $response
                    ->render('Error', ['status' => $response->statusCode()])
                    ->rootView('web::app')
                    ->withSharedData()
                    ->usingMiddleware(HandleInertiaRequests::class);
            }
        });
    }

    public function register(): void
    {
        $this->mergeConfigurations();

        $this->app->singleton(WebJobsRepository::class);
    }

    private function addPublications(): void
    {
        $this->publishes([
            $this->getBaseFilePath().'package.json' => base_path('package.json'),
            $this->getBaseFilePath().'tailwind.config.js' => base_path('tailwind.config.js'),
            $this->getBaseFilePath().'postcss.config.js' => base_path('postcss.config.js'),
            $this->getBaseFilePath().'vite.config.js' => base_path('vite.config.js'),
        ], 'web-static');

        /*
         * to publish assets one can run:
         * php artisan vendor:publish --tag=web --force
         * or use Laravel Mix to copy the folder to public repo of core.
         */
        $this->publishes([
            __DIR__.'/../public/img' => public_path('img'),
            __DIR__.'/../resources/js' => resource_path('js'),
            __DIR__.'/../resources/css' => resource_path('css'),
            // publish the I18n vendor file too
            base_path('vendor/conedevelopment/i18n/resources/js') => resource_path('js/vendor'),
        ], 'web');
    }

    private function addMiddleware(): void
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

        // Inertia.JS adding
        // $router->pushMiddlewareToGroup('web', Middleware::class);
        $router->pushMiddlewareToGroup('web', HandleInertiaRequests::class);

        // Add acl-permission Middelware
        $router->aliasMiddleware('permission', PermissionMiddleware::class);
    }

    private function getBaseFilePath(): string
    {
        return __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
    }

    private function mergeConfigurations(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/web.config.php',
            'web.config'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/package.sidebar.php',
            'package.sidebar'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/web.permissions.php',
            'web.permissions'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/web.settings.php',
            'web.settings'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/web.cronExpressions.php',
            'web.cronExpressions'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/web.images.php',
            'web.images'
        );
    }

    private function addCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AssignSuperuser::class,
            ]);
        }
    }
}
