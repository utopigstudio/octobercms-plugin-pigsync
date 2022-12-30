<?php
return [
    'plugin' => [
        'name' => 'Pigsync',
        'description' => 'Content synchronisation utilities'
    ],
    'permissions' => [
        'settings' => 'Manage Pigsync Settings',
    ],
    'settings' => [
        'github' => [
            'tab' => 'Github',
            'sync_enabled' => 'Enable synchronisation',
            'team' => 'Team / owner',
            'repository' => 'Name of repository',
            'token' => 'Token',
            'branch' => 'Branch',
            'message' => 'Message'
        ]
    ]
];