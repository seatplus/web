<?php

return [
    'home'     => [
        'name'  => 'Home',
        'icon'  => 'fa fa-th',
        'route' => 'home',
    ],
    'settings' => [
        'name'    => 'Settings',
        'icon'    => 'fas fa-cogs',
        'route'   => 'home',
        'entries' => [
            [
                'name'  => 'help',
                'icon'  => 'far fa-question-circle',
                'route' => 'home',
            ],
        ],
    ],
];