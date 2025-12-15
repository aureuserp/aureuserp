<?php

return [
    'navigation' => [
        'title' => 'Edit Vendor Price List',
    ],

    'notification' => [
        'title' => 'Vendor Price updated',
        'body'  => 'The vendor price has been updated successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Vendor Price deleted',
                    'body'  => 'The vendor price has been deleted successfully.',
                ],

                'error' => [
                    'title' => 'Vendor Price could not be deleted',
                    'body'  => 'The vendor price cannot be deleted because it is currently in use.',
                ],
            ],
        ],
    ],
];
