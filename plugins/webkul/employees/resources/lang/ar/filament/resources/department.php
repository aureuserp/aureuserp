<?php

return [
    'title' => 'Departments',

    'navigation' => [
        'title' => 'Departments',
        'group' => 'Employees',
    ],

    'global-search' => [
        'name'               => 'Department',
        'department-manager' => 'Manager',
        'company'            => 'Company',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General Information',

                'fields' => [
                    'name'                => 'Name',
                    'manager'             => 'Manager',
                    'parent-department'   => 'Parent Department',
                    'manager-placeholder' => 'Select Manager',
                    'company'             => 'Company',
                    'company-placeholder' => 'Select Company',
                    'color'               => 'Color',
                ],
            ],

            'additional' => [
                'title'       => 'Additional Information',
                'description' => 'Additional information about this department.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Name',
            'manager-name' => 'Manager',
            'company-name' => 'Company',
        ],

        'groups' => [
            'name'       => 'Name',
            'manager'    => 'Manager',
            'company'    => 'Company',
            'updated-at' => 'Updated At',
            'created-at' => 'Created At',
        ],

        'filters' => [
            'name'         => 'Name',
            'manager-name' => 'Manager',
            'company-name' => 'Company',
            'updated-at'   => 'Updated At',
            'created-at'   => 'Created At',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Department restored',
                    'body'  => 'The department has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Department deleted',
                    'body'  => 'The department has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Department force deleted',
                    'body'  => 'The department has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Departments restored',
                    'body'  => 'The departments has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Departments deleted',
                    'body'  => 'The departments has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Departments force deleted',
                    'body'  => 'The departments has been force deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'name'            => 'Name',
                    'manager'         => 'Manager',
                    'company'         => 'Company',
                    'color'           => 'Color',
                    'hierarchy-title' => 'Department Organization',
                ],
            ],
        ],
    ],
];
