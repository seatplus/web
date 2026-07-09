<?php

declare(strict_types=1);

namespace Seatplus\Web\Support;

use Illuminate\Support\Facades\Lang;

class Translations
{
    /**
     * Build the `window.translations`-shaped structure for the given group keys in the
     * current (or given) locale, with fallback-locale values merged underneath per key.
     *
     * @param  list<string>  $groups
     * @return array<string, mixed>
     */
    public static function gather(array $groups, ?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $fallback = (string) config('app.fallback_locale');

        $bag = [];

        foreach (array_unique($groups) as $group) {
            $lines = self::lines($group, $locale, $fallback);

            if (str_contains($group, '::')) {
                [$namespace, $name] = explode('::', $group, 2);
                $bag[$namespace.'::'][$name] = $lines;

                continue;
            }

            $bag[$group] = $lines;
        }

        return $bag;
    }

    /**
     * @return array<string, mixed>
     */
    private static function lines(string $group, string $locale, string $fallback): array
    {
        $current = self::raw($group, $locale);

        if ($locale === $fallback) {
            return $current;
        }

        return array_merge(self::raw($group, $fallback), $current);
    }

    /**
     * The raw translation array for a group in one locale (no built-in fallback).
     *
     * @return array<string, mixed>
     */
    private static function raw(string $group, string $locale): array
    {
        $lines = Lang::get($group, [], $locale, false);

        return is_array($lines) ? $lines : [];
    }
}
