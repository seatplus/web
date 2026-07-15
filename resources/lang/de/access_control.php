<?php

declare(strict_types=1);

return [
    'group' => 'Gruppe',
    'groups' => 'Gruppen',
    'members_count' => '{0} Keine Mitglieder|{1} :count Mitglied|[2,*] :count Mitglieder',
    'delete_confirm' => 'Gruppe ":name" endgültig löschen? Dies kann nicht rückgängig gemacht werden.',

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
        'everyone_help' => 'Jeder Nutzer ist berechtigt, unabhängig von Corporation oder Allianz.',
        'none' => 'Noch niemand',
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
        'everything_help' => 'Die Berechtigungen der Gruppe gelten für jeden Charakter, jede Corporation und Allianz.',
        'none' => 'Nichts',
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
        'save' => 'Speichern',
        'cancel' => 'Abbrechen',
        'add' => 'Hinzufügen',
    ],

    'fields' => [
        'name' => 'Name',
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
        'all_groups' => 'Alle Gruppen',
        'available' => 'Verfügbar zum Beitritt',
        'none_available' => 'Keine Gruppen zum Beitritt verfügbar.',
        'no_groups' => 'Du bist noch in keiner Gruppe.',
    ],

    'permissions' => [
        'help' => 'Welche Berechtigungen diese Gruppe ihren Mitgliedern gewährt.',
        'search' => 'Berechtigungen filtern…',
        'none' => 'Keine passenden Berechtigungen.',
        'none_selected' => 'Keine',
        'selected' => ':count ausgewählt',
    ],

    'wizard' => [
        'title' => 'Neue Gruppe',
        'back' => 'Zurück',
        'next' => 'Weiter',
        'review' => 'Überprüfe die Gruppe, bevor du sie erstellst.',
        'steps' => [
            'name' => 'Name',
            'join_method' => 'Beitrittsart',
            'eligibility' => 'Berechtigung',
            'applies_to' => 'Gilt für',
            'permissions' => 'Berechtigungen',
            'review' => 'Überprüfen',
        ],
    ],
];
