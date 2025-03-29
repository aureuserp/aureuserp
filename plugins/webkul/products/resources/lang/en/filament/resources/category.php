<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'name'             => 'Name',
                    'name-placeholder' => 'eg. Lamps',
                    'parent'           => 'Parent',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Name',
            'full-name'   => 'Full Name',
            'parent-path' => 'Parent Path',
            'parent'      => 'Parent',
            'creator'     => 'Creator',
            'created-at'  => 'Created At',
            'created-at'  => 'Created At',
            'updated-at'  => 'Updated At',
        ],

        'groups' => [
            'parent'     => 'Parent',
            'creator' => 'Creator',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'filters' => [
            'parent' => 'Parent',
            'creator' => 'Creator',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Category deleted',
                        'body'  => 'The Category has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Category could not be deleted',
                        'body'  => 'The category cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Categories deleted',
                        'body'  => 'The categories has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Categories could not be deleted',
                        'body'  => 'The categories cannot be deleted because they are currently in use.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General Information',

                'entries' => [
                    'name'        => 'Name',
                    'parent'      => 'Parent Category',
                    'full_name'   => 'Full Category Name',
                    'parent_path' => 'Category Path',
                ],
            ],

            'record-information' => [
                'title' => 'Record Information',

                'entries' => [
                    'creator'    => 'Created By',
                    'created_at' => 'Created At',
                    'updated_at' => 'Last Updated At',
                ],
            ],
        ],
    ],
];
