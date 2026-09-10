<?php

return [
    'title' => 'Edit Currency',

    'notification' => [
        'title' => 'Currency updated',
        'body'  => 'The currency has been updated successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Currency deleted',
                'body'  => 'The currency has been deleted successfully.',

                'error' => [
                    'title' => 'Currency could not be deleted',
                    'body'  => 'The currency cannot be deleted because it is currently in use.',
                ],
            ],
        ],
    ],
];
