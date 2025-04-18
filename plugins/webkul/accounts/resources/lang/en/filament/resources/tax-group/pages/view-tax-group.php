<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Tax group deleted',
                    'body'  => 'The tax group has been deleted successfully.',
                ],

                'error' => [
                    'title' => 'Tax Group could not be deleted',
                    'body'  => 'The tax group cannot be deleted because it is currently in use.',
                ],
            ],
        ],
    ],
];
