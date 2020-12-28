<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020 Felix Huber
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

use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use Inertia\Middleware;
use Seatplus\Auth\Models\User;
use Seatplus\Web\Console\Commands\AssignSuperuser;
use Seatplus\Web\Http\Middleware\Authenticate;
use Seatplus\Web\Http\Middleware\CheckPermissionAffiliation;
use Seatplus\Web\Http\Middleware\Locale;
use Seatplus\Web\Http\Resources\UserRessource;
use Seatplus\Web\Services\Sidebar\SidebarEntries;

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

        // Add commands
        $this->addCommands();
    }

    public function register()
    {
        // Register any extra services
        $this->register_services();

        $this->registerIntertiaJs();

        $this->mergeConfigurations();
    }

    private function addPublications()
    {
        /*
         * to publish assets one can run:
         * php artisan vendor:publish --tag=web --force
         * or use Laravel Mix to copy the folder to public repo of core.
         */
        $this->publishes([
            __DIR__ . '/public/img' => public_path('img'),
            __DIR__ . '/resources/js' => resource_path('js'),
            __DIR__ . '/resources/sass' => resource_path('sass'),
            $this->getPackageJsonFile() => base_path('package.json'),
            $this->getPackageLockJsonFile() => base_path('package-lock.json'),
            $this->getPackageTailwindConfig() => base_path('tailwind.config.js'),
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

        // Inertia.JS adding
        $router->pushMiddlewareToGroup('web', Middleware::class);

        // Add permission Middelware
        $router->aliasMiddleware('permission', CheckPermissionAffiliation::class);
    }

    private function register_services()
    {
    }

    private function getPackageJsonFile()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'package.json';
    }

    private function getPackageLockJsonFile()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'package-lock.json';
    }

    private function getPackageTailwindConfig()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tailwind.config.js';
    }

    private function registerIntertiaJs()
    {
        Inertia::setRootView('web::app');

        Inertia::version(function () {
            return md5_file(public_path('mix-manifest.json'));
        });

        Inertia::share([
            'flash' => function () {
                return [
                    'success' => session()->pull('success'),
                    'info' => session()->pull('info'),
                    'warning' => session()->pull('warning'),
                    'error' => session()->pull('error'),
                ];
            },
            'sidebar' => function () {
                return auth()->guest() ? [] : (new SidebarEntries)->filter();
            },
            'user' => function () {
                return auth()->guest() ? '' : UserRessource::make(
                    User::with('main_character', 'characters', 'characters.refresh_token')
                        ->where('id', auth()->user()->id)
                        ->first()
                );
            },
            'translation' => function () {
                return [
                    'success' => trans('web::notifications.success'),
                    'info' => trans('web::notifications.info'),
                    'warning' => trans('web::notifications.warning'),
                    'error' => trans('web::notifications.error'),
                ];
            },
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
            'images' => function () {
                return [
                    'logo' => asset('img/seat_plus.svg'),
                ];
            },
        ]);
    }

    private function mergeConfigurations()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/web.config.php', 'web.config'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/config/package.sidebar.php', 'package.sidebar'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/config/web.permissions.php', 'web.permissions'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/config/web.settings.php', 'web.settings'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/config/web.cronExpressions.php', 'web.cronExpressions'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/config/web.jobs.php', 'web.jobs'
        );
    }

    private function addCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AssignSuperuser::class,
            ]);
        }
    }
}
