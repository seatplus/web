<?php

declare(strict_types=1);

return [
    // Translation groups shipped to the SPA on every page — the chrome rendered by the
    // persistent layout (toasts / notifications). `wallet_journal` is here for now because
    // it renders via shared components used across several pages; Phase 2 can move it onto
    // just the wallet/recruitment controllers via the SharesTranslations trait.
    //
    // Page-specific groups are declared per-controller with `$this->shareTranslations([...])`
    // (or Translations::need([...]) from middleware) and merged in for the current locale.
    'shared' => [
        'web::notifications',
        'web::toasts',
        'web::wallet_journal',
    ],
];
