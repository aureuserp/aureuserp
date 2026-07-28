<?php

return [
    'navigation' => [
        'title' => 'Categories',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General Information',

                'fields' => [
                    'name'       => 'Name',
                    'technician' => 'Responsible',
                    'company'    => 'Company',
                    'note'       => 'Note',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Name',
            'technician' => 'Responsible',
            'company'    => 'Company',
            'created-at' => 'Created At',
        ],

        'groups' => [
            'technician' => 'Responsible',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Category updated',
                    'body'  => 'The category has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Category deleted',
                    'body'  => 'The category has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categories deleted',
                    'body'  => 'The categories have been deleted successfully.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Category created',
                    'body'  => 'The category has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General Information',

                'entries' => [
                    'name'       => 'Name',
                    'technician' => 'Responsible',
                    'company'    => 'Company',
                    'note'       => 'Note',
                ],
            ],
        ],
    ],
];
