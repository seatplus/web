<?php


namespace Seatplus\Web\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class Locale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     * @throws \Seatplus\Web\Exceptions\SettingException
     */
    public function handle($request, Closure $next)
    {

        App::setLocale(setting('language'));

        return $next($request);
    }
}
