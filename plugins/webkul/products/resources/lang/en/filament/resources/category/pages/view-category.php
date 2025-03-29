<?php

return [
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
];
