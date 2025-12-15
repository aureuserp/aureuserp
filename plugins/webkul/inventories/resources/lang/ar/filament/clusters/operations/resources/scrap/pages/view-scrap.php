<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Scrap Deleted',
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
