<?php

declare(strict_types=1);

namespace Seatplus\Web\Support;

use Illuminate\Support\Facades\File;

class Locales
{
    /**
     * The UI locales the app offers — the union of locale directories across every
     * registered translation namespace (web, auth, and any plugin such as
     * `notifications::`). Ship `resources/lang/<code>` from any package and it becomes
     * offerable; display names are rendered client-side via the browser's Intl.DisplayNames.
     *
     * @return list<string>
     */
    public static function available(): array
    {
        $locales = [];

        foreach (app('translator')->getLoader()->namespaces() as $path) {
            if (! is_dir($path)) {
                continue;
            }

            foreach (File::directories($path) as $dir) {
                $locales[] = basename($dir);
            }
        }

        $locales = array_unique($locales);
        sort($locales);

        return $locales === [] ? [(string) config('app.fallback_locale')] : $locales;
    }
}
