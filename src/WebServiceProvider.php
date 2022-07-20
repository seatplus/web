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

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;
use Seatplus\Auth\Services\Affiliations\GetAffiliatedIdsService;
use Seatplus\Auth\Services\Affiliations\GetOwnedAffiliatedIdsService;
use Seatplus\Auth\Services\Dtos\AffiliationsDto;
use Seatplus\Web\Console\Commands\AssignSuperuser;
use Seatplus\Web\Exception\Handler;
use Seatplus\Web\Http\Middleware\Authenticate;
use Seatplus\Web\Http\Middleware\CheckACLPermission;
use Seatplus\Web\Http\Middleware\CheckPermissionAndAffiliation;
use Seatplus\Web\Http\Middleware\HandleInertiaRequests;
use Seatplus\Web\Http\Middleware\HasPermission;
use Seatplus\Web\Http\Middleware\Locale;
use Seatplus\Web\Services\GetRecruitIdsService;

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
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');

        // Add views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'web');

        //Add Migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');

        // Add translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'web');

        // Add Middlewares
        $this->addMiddleware();

        // Add commands
        $this->addCommands();

        // Add query macros
        $this->addQueryMacros();
    }

    public function register()
    {
        // Register any extra services
        $this->register_services();

        //$this->registerIntertiaJs();

        $this->mergeConfigurations();

        $this->app->singleton(ExceptionHandler::class, Handler::class);
    }

    private function addPublications()
    {
        /*
         * to publish assets one can run:
         * php artisan vendor:publish --tag=web --force
         * or use Laravel Mix to copy the folder to public repo of core.
         */
        $this->publishes([
            __DIR__ . '/../public/img' => public_path('img'),
            __DIR__ . '/../resources/js' => resource_path('js'),
            __DIR__ . '/../resources/css' => resource_path('css'),
            $this->getPackageJsonFile() => base_path('package.json'),
            $this->getPackageTailwindConfig() => base_path('tailwind.config.js'),
            // publish teh I18n vendor file too
            base_path('vendor/conedevelopment/i18n/resources/js') => resource_path('js/vendor'),
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
        //$router->pushMiddlewareToGroup('web', Middleware::class);
        $router->pushMiddlewareToGroup('web', HandleInertiaRequests::class);

        // Add permission Middelware
        $router->aliasMiddleware('permission', CheckPermissionAndAffiliation::class);

        // Add acl-permission Middelware
        $router->aliasMiddleware('acl-permission', CheckACLPermission::class);
    }

    private function register_services()
    {
    }

    private function getPackageJsonFile()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'package.json';
    }

    private function getPackageTailwindConfig()
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tailwind.config.js';
    }

    private function mergeConfigurations()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/web.config.php',
            'web.config'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../config/package.sidebar.php',
            'package.sidebar'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../config/web.permissions.php',
            'web.permissions'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../config/web.settings.php',
            'web.settings'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../config/web.cronExpressions.php',
            'web.cronExpressions'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/../config/web.jobs.php',
            'web.jobs'
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

    private function addQueryMacros()
    {
        Builder::macro('whereAffiliatedCorporation', function (AffiliationsDto $affiliationsDto) {

            $affiliated_ids = GetAffiliatedIdsService::make($affiliationsDto)->getQuery();
            $owned_ids = GetOwnedAffiliatedIdsService::make($affiliationsDto)->getQuery();

            return $this->when(! $affiliationsDto->user->can('superuser'), fn(Builder $query) => $query
                ->joinSub(
                    $affiliated_ids->union($owned_ids),
                    'affiliated',
                    'corporation_infos.corporation_id',
                    '=',
                    'affiliated.affiliated_id'
                )
            );
        });

        Builder::macro('whereAffiliatedCharacters', function (AffiliationsDto $affiliationsDto, ) {

            $affiliated_ids = GetAffiliatedIdsService::make($affiliationsDto)->getQuery();
            $owned_ids = GetOwnedAffiliatedIdsService::make($affiliationsDto)->getQuery();

            return $this->when(! $affiliationsDto->user->can('superuser'), fn(Builder $query) => $query
                ->joinSub(
                    $affiliated_ids->union($owned_ids),
                    'affiliated',
                    'character_infos.character_id',
                    '=',
                    'affiliated.affiliated_id'
                )
            );
        });
    }
}
