<?php

return [
    'notification' => [
        'title' => 'Scrap updated',
        'body'  => 'The scrap has been updated successfully.',
    ],

    'header-actions' => [
        'validate' => [
            'label' => 'Validate',

            'notification' => [
                'warning' => [
                    'title' => 'Insufficient stock',
                    'body'  => 'The scrap has insufficient stock to validate.',
                ],

                'success' => [
                    'title' => 'Scrap marked as done',
                    'body'  => 'The scrap has been marked as done successfully.',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Scrap deleted',
                    'body'  => 'The scrap has been deleted successfully.',
                ],

                'error' => [
                    'title' => 'Scraps could not be deleted',
                    'body'  => 'The scraps cannot be deleted because they are currently in use.',
                ],
            ],
        ],
    ],
];
