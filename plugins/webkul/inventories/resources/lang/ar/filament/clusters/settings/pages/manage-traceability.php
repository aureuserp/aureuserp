<?php

return [
    'title' => 'Manage Traceability',

    'form' => [
        'enable-lots-serial-numbers'                             => 'Lots & Serial Numbers',
        'enable-lots-serial-numbers-helper-text'                 => 'Get a full traceability from vendors to customers',
        'configure-lots'                                         => 'Configure Lots',
        'enable-expiration-dates'                                => 'Expiration Dates',
        'enable-expiration-dates-helper-text'                    => 'Set expiration dates on lots & serial numbers',
        'display-on-delivery-slips'                              => 'Display on Delivery Slips',
        'display-on-delivery-slips-helper-text'                  => 'Lots & Serial numbers will appear on the delivery slips',
        'display-expiration-dates-on-delivery-slips'             => 'Display Expiration Dates on Delivery Slips',
        'display-expiration-dates-on-delivery-slips-helper-text' => 'Expiration dates will appear on the delivery slip',
        'enable-consignments'                                    => 'Consignments',
        'enable-consignments-helper-text'                        => 'Set owner on stored products',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'You have products in stock that have lot/serial number tracking enabled. ',
                'body'  => 'First switch off tracking on all the products before switching off this setting.',
            ],
        ],
    ],
];
