<?php

namespace Seatplus\Web\Tests\Stubs;

use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Session\Middleware\StartSession;
use Orchestra\Testbench\Foundation\Http\Kernel as OrchestraHttpKernel;
use Seatplus\Web\Http\Middleware\Authenticate;

class Kernel extends OrchestraHttpKernel
{
    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => Authenticate::class,
        'guest' => RedirectIfAuthenticated::class,
    ];

    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        StartSession::class,
    ];
}
