<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Tax deleted',
                    'body'  => 'The tax has been deleted successfully.',
                ],

                'error' => [
                    'title' => 'Tax could not be deleted',
                    'body'  => 'The tax cannot be deleted because it is currently in use.',
                ],
            ],
        ],
    ],
];
