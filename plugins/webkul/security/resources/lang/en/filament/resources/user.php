<?php

return [
    'title' => 'Users',

    'navigation' => [
        'title' => 'Users',
        'group' => 'Settings',
    ],

    'global-search' => [
        'email' => 'Email',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title'  => 'General Information',
                'fields' => [
                    'name'                  => 'Name',
                    'email'                 => 'Email',
                    'password'              => 'Password',
                    'password-confirmation' => 'Password Confirmation',
                ],
            ],

            'permissions' => [
                'title'  => 'Permissions',
                'fields' => [
                    'roles'                                    => 'Roles',
                    'permissions'                              => 'Permissions',
                    'resource-permission'                      => 'Resource Permission',
                    'resource-permission-self-change-disabled' => 'You cannot change your own resource permission. Ask another administrator to update it.',
                    'teams'                                    => 'Teams',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'  => 'Language & Status',
                'fields' => [
                    'language' => 'Preferred Language',
                    'status'   => 'Status',
                ],
            ],

            'multi-company' => [
                'title'             => 'Multi Company',
                'allowed-companies' => 'Allowed Companies',
                'default-company'   => 'Default Company',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'avatar'              => 'Avatar',
            'name'                => 'Name',
            'email'               => 'Email',
            'teams'               => 'Teams',
            'role'                => 'Role',
            'resource-permission' => 'Resource Permission',
            'default-company'     => 'Default Company',
            'allowed-company'     => 'Allowed Company',
            'created-by'          => 'Created By',
            'created-at'          => 'Created At',
            'updated-at'          => 'Updated At',
        ],

        'filters' => [
            'resource-permission' => 'Resource Permission',
            'teams'               => 'Teams',
            'roles'               => 'Roles',
            'default-company'     => 'Default Company',
            'allowed-companies'   => 'Allowed Companies',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'User edited',
                    'body'  => 'The user has been edited successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'User deleted',
                    'body'  => 'The user has been deleted successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'User restored',
                    'body'  => 'The user has been restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Users restored',
                    'body'  => 'The users has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Users deleted',
                    'body'  => 'The users has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Users force deleted',
                    'body'  => 'The users has been force deleted successfully.',
                    'error' => [
                        'title' => 'User could not be deleted',
                        'body'  => 'The user cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Users created',
                    'body'  => 'The users has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title'   => 'General Information',
                'entries' => [
                    'name'                  => 'Name',
                    'email'                 => 'Email',
                    'password'              => 'Password',
                    'password-confirmation' => 'Password Confirmation',
                ],
            ],

            'permissions' => [
                'title'   => 'Permissions',
                'entries' => [
                    'roles'               => 'Roles',
                    'permissions'         => 'Permissions',
                    'resource-permission' => 'Resource Permission',
                    'teams'               => 'Teams',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'   => 'Language & Status',
                'entries' => [
                    'language' => 'Preferred Language',
                    'status'   => 'Status',
                ],
            ],

            'multi-company' => [
                'title'             => 'Multi Company',
                'allowed-companies' => 'Allowed Companies',
                'default-company'   => 'Default Company',
            ],
        ],
    ],
];
