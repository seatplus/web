<?php

declare(strict_types=1);

return [
    // Translation groups shipped to the SPA on every page — the notification labels
    // rendered by the persistent layout (Toast.vue).
    //
    // Page-specific groups are declared per-controller with `$this->shareTranslations([...])`
    // (or Translations::need([...]) from middleware) and merged in for the current locale —
    // e.g. the wallet/recruitment/compliance controllers declare `web::wallet_journal`.
    'shared' => [
        'web::notifications',
    ],
];
