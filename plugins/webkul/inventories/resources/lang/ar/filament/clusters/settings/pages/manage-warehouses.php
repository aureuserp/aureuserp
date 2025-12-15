<?php

return [
    'title' => 'Manage Warehouses',

    'form' => [
        'enable-locations'                      => 'Locations',
        'enable-locations-helper-text'          => 'Track product location in your warehouse',
        'configure-locations'                   => 'Configure Locations',
        'enable-multi-steps-routes'             => 'Multi Steps Routes',
        'enable-multi-steps-routes-helper-text' => 'Use your own routes to manage the transfer of products between warehouses',
        'configure-routes'                      => 'Configure Warehouse Routes',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Have multiple warehouses',
                'body'  => 'You can\'t deactivate the multi-location if you have more than one warehouse.',
            ],
        ],
    ],
];
