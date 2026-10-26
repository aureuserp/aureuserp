<?php

return [
    'permission-descriptions' => [
        'view_any'         => 'List and browse all records.',
        'view'             => 'View a single record in detail.',
        'create'           => 'Create new records.',
        'update'           => 'Edit and save existing records.',
        'delete'           => 'Delete a single record.',
        'delete_any'       => 'Delete multiple records at once.',
        'force_delete'     => 'Permanently delete a record.',
        'force_delete_any' => 'Permanently delete multiple records.',
        'restore'          => 'Restore a single archived record.',
        'restore_any'      => 'Restore multiple archived records.',
        'reorder'          => 'Change the sort order of records.',
    ],

    'form' => [
        'fields' => [
            'web'     => 'Web',
            'sanctum' => 'Sanctum',
        ],
    ],

    'notification' => [
        'system-role-delete' => [
            'title' => 'System Role Cannot Be Deleted',
            'body'  => 'This is a system role and cannot be deleted.',
        ],
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Role deleted',
                    'body'  => 'The role has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Roles deleted',
                    'body'  => 'The selected roles have been deleted successfully.',
                ],
            ],
        ],
    ],
];
