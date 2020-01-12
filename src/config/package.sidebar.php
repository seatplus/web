<?php

return [
    'character' => [
        [
            'name' => 'assets',
            'icon'  => 'fas fa-dolly-flatbed',
            'route' => 'character.assets',
        ],
    ],
    'home'     => [
        [
            'name'  => 'Home',
            'icon'  => 'fa fa-th',
            'route' => 'home',
        ],
    ],
    'settings' => [
        [
            'name'    => 'Settings',
            'icon'    => 'fas fa-cogs',
            'route'   => '',
            'entries' => [
                [
                    'name'  => 'help',
                    'icon'  => 'far fa-question-circle',
                    'route' => 'settings',
                ],
            ],
        ],
    ],
];
