<?php

return [
    'notification' => [
        'title' => 'Category updated',
        'body'  => 'The category has been updated successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Category deleted',
                    'body'  => 'The category has been deleted successfully.',
                ],

                'error' => [
                    'title' => 'Category could not be deleted',
                    'body'  => 'The category cannot be deleted because it is currently in use.',
                ],
            ],
        ],
    ],

    'save' => [
        'notification' => [
            'error' => [
                'title' => 'Category updated failed',
            ],
        ],
    ],
];
