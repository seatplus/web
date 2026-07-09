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
use Seatplus\Web\Console\Commands\CheckTranslationKeys;
use Seatplus\Web\Contracts\WebJobsRepository;
use Seatplus\Web\Http\Middleware\Authenticate;
use Seatplus\Web\Http\Middleware\HandleInertiaRequests;
use Seatplus\Web\Http\Middleware\SetLocale;
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

        // Default the Inertia root view to this package's blade at the provider
        // level (not only via HandleInertiaRequests::rootView()), so it applies
        // even when the per-request middleware isn't run — e.g. Pest browser's
        // in-process server, which doesn't apply provider-pushed group middleware.
        Inertia::setRootView('web::app');

        // Add Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations/');

        // Add translations
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'web');

        // Add Middlewares
        $this->addMiddleware();

        // Add commands
        $this->addCommands();

        // Register Inertia error page handler.
        //
        // Inertia v3 removed the v2 modal overlay for non-Inertia responses, so we must
        // render Error.vue for Inertia navigation requests (X-Inertia header) in all envs.
        //
        // For initial page loads (no X-Inertia header) in local+debug mode we return null,
        // which lets Laravel's Ignition page render for 500/503 — useful for development.
        // In production, all 4xx/5xx render Error.vue.
        Inertia::handleExceptionsUsing(function (ExceptionResponse $response) {
            $code = $response->statusCode();

            if (! in_array($code, [403, 404, 500, 503])) {
                return;
            }

            // Allow Ignition to handle 500/503 on initial page loads in local debug mode.
            $isInertiaRequest = $response->request->hasHeader('X-Inertia');
            if (! $isInertiaRequest && app()->isLocal() && config('app.debug') && in_array($code, [500, 503])) {
                return;
            }

            return $response
                ->render('Error', ['status' => $code])
                ->rootView('web::app')
                ->withSharedData()
                ->usingMiddleware(HandleInertiaRequests::class);
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
         * Localization: resolve + set the request locale before controllers/props run.
         */
        $router->pushMiddlewareToGroup('web', SetLocale::class);

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

        $this->mergeConfigFrom(
            __DIR__.'/../config/web.locales.php',
            'web.locales'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/web.translations.php',
            'web.translations'
        );
    }

    private function addCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AssignSuperuser::class,
                CheckTranslationKeys::class,
            ]);
        }
    }
}
