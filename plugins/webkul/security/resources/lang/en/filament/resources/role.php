<?php

return [
    'form' => [
        'fields' => [
            'web'     => 'Web',
            'sanctum' => 'Sanctum',
            'select-all-permissions'      => 'Select all permissions',
            'select-all-permissions-hint' => 'Grant every permission to this role. Turn off to clear all.',
        ],
    ],

    'notification' => [
        'system-role-delete' => [
            'title' => 'System Role Cannot Be Deleted',
            'body'  => 'This is a system role and cannot be deleted.',
        ],
    ],

    'matrix' => [
        'title'        => 'Permissions',
        'all-modules'  => 'All plugins:',
        'select-all'   => 'Select all',
        'deselect-all' => 'Deselect all',
        'search'       => 'Search plugins…',
        'model'        => 'Model',
        'action'       => 'Select Action',
        'all'          => 'all',
        'none'         => 'none',
        'granted'      => 'granted',
        'pages'        => 'Pages',
        'widgets'      => 'Widgets',
        'empty'        => 'No permissions available.',
    ],
];
