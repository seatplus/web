<?php

declare(strict_types=1);

namespace Seatplus\Web\Support;

use Illuminate\Support\Facades\File;

class Locales
{
    /**
     * The UI locales the app offers, derived from the `web` package's lang directories —
     * add `resources/lang/<code>` and it appears (no config needed). Display names are
     * rendered client-side via the browser's Intl.DisplayNames.
     *
     * @return list<string>
     */
    public static function available(): array
    {
        $path = app('translator')->getLoader()->namespaces()['web'] ?? null;

        if (! is_string($path) || ! is_dir($path)) {
            return [(string) config('app.fallback_locale')];
        }

        return collect(File::directories($path))
            ->map(fn (string $dir): string => basename($dir))
            ->sort()
            ->values()
            ->all();
    }
}
