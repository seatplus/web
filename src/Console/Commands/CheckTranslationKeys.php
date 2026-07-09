<?php

declare(strict_types=1);

namespace Seatplus\Web\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Seatplus\Web\Support\Locales;

class CheckTranslationKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seatplus:i18n:missing-keys
                            {--strict : Exit with a non-zero code when any locale is missing keys}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Report web translation keys present in the fallback locale but missing from the others';

    public function handle(): int
    {
        $langPath = dirname(__DIR__, 3).'/resources/lang';
        $fallback = config('app.fallback_locale', 'en');

        if (! is_dir("{$langPath}/{$fallback}")) {
            $this->error("Fallback locale directory [{$fallback}] not found in {$langPath}.");

            return self::FAILURE;
        }

        $fallbackKeys = $this->keysFor("{$langPath}/{$fallback}");
        $hasGaps = false;

        foreach (Locales::available() as $locale) {
            if ($locale === $fallback) {
                continue;
            }

            $localeKeys = is_dir("{$langPath}/{$locale}")
                ? $this->keysFor("{$langPath}/{$locale}")
                : [];

            $missing = array_values(array_diff($fallbackKeys, $localeKeys));

            if ($missing === []) {
                $this->info(sprintf('[%s] complete (%d keys).', $locale, count($fallbackKeys)));

                continue;
            }

            $hasGaps = true;
            $this->warn(sprintf('[%s] missing %d of %d keys:', $locale, count($missing), count($fallbackKeys)));

            foreach ($missing as $key) {
                $this->line("  - {$key}");
            }
        }

        if ($hasGaps && $this->option('strict')) {
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Flatten every translation file in a locale directory to dot-notated `group.key` paths.
     *
     * @return list<string>
     */
    private function keysFor(string $dir): array
    {
        $keys = [];

        foreach (glob("{$dir}/*.php") ?: [] as $file) {
            $group = basename($file, '.php');
            $translations = require $file;

            if (! is_array($translations)) {
                continue;
            }

            foreach (array_keys(Arr::dot($translations)) as $key) {
                $keys[] = "{$group}.{$key}";
            }
        }

        return $keys;
    }
}
