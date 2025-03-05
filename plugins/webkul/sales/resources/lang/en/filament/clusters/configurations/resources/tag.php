<?php

return [
    'title' => 'Tag',

    'navigation' => [
        'title' => 'Tag',
        'group' => 'Sales Orders',
    ],

    'global-search' => [
        'name'    => 'Name',
    ],

    'form' => [
        'fields' => [
            'name'  => 'Name',
            'color' => 'Color',
        ],
    ],

    'table' => [
        'columns' => [
            'created-by' => 'Created by',
            'name'       => 'Name',
            'color'      => 'Color',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Product Tag updated',
                    'body'  => 'The product Tag has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Product Tag deleted',
                    'body'  => 'The product Tag has been deleted successfully.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Product Tag deleted',
                    'body'  => 'The product Tag has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'  => 'Name',
            'color' => 'Color',
        ],
    ],
];
