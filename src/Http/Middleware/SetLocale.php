<?php

declare(strict_types=1);

namespace Seatplus\Web\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Seatplus\Web\Support\Locales;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Resolve and set the request locale before controllers run, so controllers and the
     * shared props can safely translate against the right locale.
     */
    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale($this->resolve($request));

        return $next($request);
    }

    /**
     * user preference → session pick → browser Accept-Language → global setting → app default.
     * Only a locale in the supported registry is honoured; anything else falls back.
     */
    private function resolve(Request $request): string
    {
        $supported = Locales::available();
        $fallback = (string) config('app.locale');

        $candidate = $this->userLocale()
            ?? $this->sessionLocale($request)
            ?? $this->browserLocale($request, $supported)
            ?? setting('language')
            ?? $fallback;

        return in_array($candidate, $supported, true) ? (string) $candidate : $fallback;
    }

    /**
     * The authenticated user's stored locale, or null. Read only when the column is loaded,
     * to stay compatible with Model::shouldBeStrict() before the auth package ships it.
     */
    private function userLocale(): ?string
    {
        if (! auth()->check() || ! array_key_exists('locale', auth()->user()->getAttributes())) {
            return null;
        }

        $locale = auth()->user()->getAttribute('locale');

        return is_string($locale) ? $locale : null;
    }

    private function sessionLocale(Request $request): ?string
    {
        if (! $request->hasSession()) {
            return null;
        }

        $locale = $request->session()->get('locale');

        return is_string($locale) ? $locale : null;
    }

    /**
     * The first browser-preferred (Accept-Language) locale that we support, or null.
     *
     * @param  list<string>  $supported
     */
    private function browserLocale(Request $request, array $supported): ?string
    {
        foreach ($request->getLanguages() as $language) {
            $code = strtolower(substr((string) $language, 0, 2));

            if (in_array($code, $supported, true)) {
                return $code;
            }
        }

        return null;
    }
}
