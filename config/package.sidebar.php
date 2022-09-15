<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

return [
    'home'     => [
        [
            'name'  => 'Home',
            'route' => 'home',
            'icon' => 'HomeIcon',
        ],
    ],
    'character' => [
        [
            'name' => 'Assets',
            'route' => 'character.assets',
            'icon' => 'ArchiveBoxIcon',
        ],
        [
            'name' => 'Contacts',
            'route' => 'character.contacts',
            'icon' => 'UsersIcon',
        ],
        [
            'name' => 'Wallets',
            'route' => 'character.wallets',
            'icon' => 'BanknotesIcon',
        ],
        [
            'name' => 'Contracts',
            'route' => 'character.contracts',
            'icon' => 'PencilSquareIcon',
        ],
        [
            'name' => 'Skills',
            'route' => 'character.skills',
            'icon' => 'AcademicCapIcon',
        ],
        [
            'name' => 'Mails',
            'route' => 'character.mails',
            'icon' => 'InboxIcon',
        ],
    ],
    'corporation' => [
        [
            'name' => 'Wallets',
            'permission' => 'wallets',
            'character_role' => 'Accountant|Junior_Accountant',
            'route' => 'corporation.wallet',
            'icon' => 'BanknotesIcon',
        ],
        [
            'name' => 'Member Tracking',
            'permission' => 'view member tracking',
            'character_role' => 'director',
            'route' => 'corporation.member_tracking',
            'icon' => 'CheckBadgeIcon',
        ],
        [
            'name' => 'Member Compliance',
            'permission' => 'view member compliance',
            'character_role' => 'director',
            'route' => 'corporation.member_compliance',
            'icon' => 'ShieldCheckIcon',
        ],
        [
            'name' => 'Recruitment',
            'permission' => 'can open or close corporations for recruitment|can accept or deny applications',
            'character_role' => 'director',
            'route' => 'corporation.recruitment',
            'icon' => 'ClipboardDocumentCheckIcon',
        ],
    ],
    'Access Control' => [
        [
            'name' => 'Control Group',
            'permission' => 'view access control',
            'route' => 'acl.groups',
            'icon' => 'UserGroupIcon',
        ],
    ],
    'settings' => [
        [
            'name'  => 'Server Settings',
            'permission' => 'superuser',
            'route' => 'server.settings',
            'icon' => 'CogIcon',
        ],
        [
            'name'  => 'Manual Locations',
            'permission' => 'manage manual locations',
            'route' => 'manage.manual_locations',
            'icon' => 'MapIcon',
        ],
    ],
];
