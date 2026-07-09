<?php

declare(strict_types=1);

namespace Seatplus\Web\Concerns;

use Seatplus\Web\Support\Translations;

trait SharesTranslations
{
    /**
     * Declare the translation groups this page needs (e.g. ['web::wallet_journal']);
     * merged into the shared `translations` Inertia prop for the current locale.
     *
     * @param  list<string>  $groups
     */
    protected function shareTranslations(array $groups): void
    {
        Translations::need($groups);
    }
}
