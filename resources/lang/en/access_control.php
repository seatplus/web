<?php

declare(strict_types=1);

return [
    // The group itself
    'group' => 'Group',
    'groups' => 'Groups',
    'members_count' => '{0} No members|{1} :count member|[2,*] :count members',
    'delete_confirm' => 'Permanently delete ":name"? This cannot be undone.',

    // Join method (role type) — WHO can be a member and how they get in.
    'join_method' => [
        'label' => 'Join method',
        'automatic' => [
            'label' => 'Automatic',
            'description' => 'Everyone eligible is added and removed automatically. No requests, no manual changes.',
        ],
        'manual' => [
            'label' => 'Managed',
            'description' => 'Moderators add and remove members by hand.',
        ],
        'on-request' => [
            'label' => 'Request to join',
            'description' => 'Eligible members request access; a moderator approves or denies.',
        ],
        'opt-in' => [
            'label' => 'Self-service',
            'description' => 'Eligible members join instantly, no approval needed.',
        ],
    ],

    // Eligibility (membership criteria) — which corps/alliances' members qualify to join / are auto-added.
    'eligibility' => [
        'label' => 'Eligibility',
        'help' => 'Which corporations or alliances\' members can join this group.',
        'everyone' => 'Anyone can join',
        'everyone_help' => 'Every user is eligible, regardless of their corporation or alliance.',
        'none' => 'No one yet',
    ],

    // Applies to (affiliations) — the entities this group\'s PERMISSIONS operate on. NOT membership.
    'applies_to' => [
        'label' => 'Applies to',
        'help' => 'The corporations, alliances or characters this group\'s permissions apply to.',
        'mode' => [
            'only_these' => 'Only these',
            'everyone_except' => 'Everyone except',
        ],
        'exclude' => 'Never (exclude)',
        'exclude_help' => 'Always excluded, even if matched above.',
        'everything' => 'Everything',
        'everything_help' => 'The group\'s permissions apply to every character, corporation and alliance.',
        'none' => 'Nothing',
    ],

    // Membership status
    'status' => [
        'active' => 'Member',
        'pending' => 'Pending',
        'none' => 'Not a member',
    ],

    // Actions
    'actions' => [
        'join' => 'Join',
        'apply' => 'Request to join',
        'leave' => 'Leave',
        'approve' => 'Approve',
        'deny' => 'Deny',
        'add_member' => 'Add member',
        'remove_member' => 'Remove',
        'add_moderator' => 'Add moderator',
        'remove_moderator' => 'Remove moderator',
        'create' => 'Create group',
        'delete' => 'Delete group',
        'configure' => 'Configure',
        'manage_members' => 'Manage members',
        'save' => 'Save',
        'cancel' => 'Cancel',
        'add' => 'Add',
    ],

    'fields' => [
        'name' => 'Name',
    ],

    // Page sections
    'sections' => [
        'membership' => 'Membership',
        'authorization' => 'Authorization',
        'permissions' => 'Permissions',
        'members' => 'Members',
        'moderators' => 'Moderators',
        'applications' => 'Pending applications',
    ],

    // Discover
    'discover' => [
        'my_groups' => 'My groups',
        'all_groups' => 'All groups',
        'available' => 'Available to join',
        'none_available' => 'No groups available to join.',
        'no_groups' => 'You are not in any groups yet.',
    ],

    // Permissions the group grants
    'permissions' => [
        'help' => 'Which permissions this group grants its members.',
        'search' => 'Filter permissions…',
        'none' => 'No matching permissions.',
        'none_selected' => 'None',
        'selected' => ':count selected',
    ],

    // Create wizard
    'wizard' => [
        'title' => 'Create group',
        'back' => 'Back',
        'next' => 'Next',
        'review' => 'Review the group before creating it.',
        'steps' => [
            'name' => 'Name',
            'join_method' => 'Join method',
            'eligibility' => 'Eligibility',
            'applies_to' => 'Applies to',
            'permissions' => 'Permissions',
            'review' => 'Review',
        ],
    ],
];
