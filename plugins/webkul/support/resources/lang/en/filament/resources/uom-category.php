<?php

return [
    'navigation' => [
        'group' => 'Settings',
        'title' => 'UOM Categories',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name' => 'Name',
                ],
            ],

            'uoms' => [
                'title' => 'Units of Measure',

                'fields' => [
                    'uoms'     => 'Units',
                    'type'     => 'Type',
                    'name'     => 'Name',
                    'factor'   => 'Factor',
                    'rounding' => 'Rounding Precision',
                ],

                'actions' => [
                    'add' => 'Add Unit',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Name',
            'uoms-count' => 'Units',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'groups' => [
            'created-at' => 'Created At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'UOM Category updated',
                    'body'  => 'The UOM category has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'UOM Category deleted',
                    'body'  => 'The UOM category has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'UOM Categories deleted',
                    'body'  => 'The UOM categories has been deleted successfully.',
                ],
            ],
        ],
    ],
];
