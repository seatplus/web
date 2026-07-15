<?php

declare(strict_types=1);

return [
    'group' => 'Gruppe',
    'groups' => 'Gruppen',

    'join_method' => [
        'label' => 'Beitrittsart',
        'automatic' => [
            'label' => 'Automatisch',
            'description' => 'Alle Berechtigten werden automatisch hinzugefügt und entfernt. Keine Anfragen, keine manuellen Änderungen.',
        ],
        'manual' => [
            'label' => 'Verwaltet',
            'description' => 'Moderatoren fügen Mitglieder von Hand hinzu und entfernen sie.',
        ],
        'on-request' => [
            'label' => 'Auf Anfrage',
            'description' => 'Berechtigte Mitglieder beantragen den Zugang; ein Moderator genehmigt oder lehnt ab.',
        ],
        'opt-in' => [
            'label' => 'Selbstbedienung',
            'description' => 'Berechtigte Mitglieder treten sofort bei, ohne Genehmigung.',
        ],
    ],

    'eligibility' => [
        'label' => 'Berechtigung',
        'help' => 'Mitglieder welcher Corporations oder Allianzen dieser Gruppe beitreten können.',
        'everyone' => 'Jeder kann beitreten',
    ],

    'applies_to' => [
        'label' => 'Gilt für',
        'help' => 'Die Corporations, Allianzen oder Charaktere, für die die Berechtigungen dieser Gruppe gelten.',
        'mode' => [
            'only_these' => 'Nur diese',
            'everyone_except' => 'Alle außer',
        ],
        'exclude' => 'Nie (ausschließen)',
        'exclude_help' => 'Immer ausgeschlossen, auch wenn oben zutreffend.',
        'everything' => 'Alles',
    ],

    'status' => [
        'active' => 'Mitglied',
        'pending' => 'Ausstehend',
        'none' => 'Kein Mitglied',
    ],

    'actions' => [
        'join' => 'Beitreten',
        'apply' => 'Beitritt anfragen',
        'leave' => 'Verlassen',
        'approve' => 'Genehmigen',
        'deny' => 'Ablehnen',
        'add_member' => 'Mitglied hinzufügen',
        'remove_member' => 'Entfernen',
        'add_moderator' => 'Moderator hinzufügen',
        'remove_moderator' => 'Moderator entfernen',
        'create' => 'Gruppe erstellen',
        'delete' => 'Gruppe löschen',
        'configure' => 'Konfigurieren',
        'manage_members' => 'Mitglieder verwalten',
    ],

    'sections' => [
        'membership' => 'Mitgliedschaft',
        'authorization' => 'Autorisierung',
        'permissions' => 'Berechtigungen',
        'members' => 'Mitglieder',
        'moderators' => 'Moderatoren',
        'applications' => 'Ausstehende Anträge',
    ],

    'discover' => [
        'my_groups' => 'Meine Gruppen',
        'available' => 'Verfügbar zum Beitritt',
        'none_available' => 'Keine Gruppen zum Beitritt verfügbar.',
        'no_groups' => 'Du bist noch in keiner Gruppe.',
    ],
];
