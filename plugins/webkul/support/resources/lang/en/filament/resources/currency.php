<?php

return [
    'title' => 'Currencies',

    'navigation' => [
        'title' => 'Currencies',
        'group' => 'Settings',
    ],

    'form' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Currency Information',

                'fields' => [
                    'name'         => 'Currency Name',
                    'name-tooltip' => 'Enter the official currency name',
                    'symbol'       => 'Currency Symbol',
                    'full-name'    => 'Full Name',
                    'iso-numeric'  => 'ISO Numeric Code',
                ],
            ],

            'format-information' => [
                'title' => 'Format Configuration',

                'fields' => [
                    'decimal-places'        => 'Decimal Places',
                    'rounding'              => 'Rounding Precision',
                    'rounding-helper-text'  => 'Set the rounding precision for currency calculations',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Status & Configuration',

                'fields' => [
                    'status' => 'Status',
                ],
            ],

            'rates' => [
                'title'       => 'Currency Rates',
                'description' => 'Manage historic exchange rates for this currency relative to the base currency (USD).',

                'fields' => [
                    'name'              => 'Date',
                    'unit-per-currency' => 'Unit Per :currency',
                    'currency-per-unit' => ':currency Per Unit',
                ],

                'add-rate'   => 'Add Rate',
                'item-label' => 'Rate',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Currency Name',
            'symbol'         => 'Symbol',
            'full-name'      => 'Full Name',
            'iso-numeric'    => 'ISO Code',
            'decimal-places' => 'Decimal Places',
            'rounding'       => 'Rounding',
            'status'         => 'Status',
            'created-at'     => 'Created At',
            'updated-at'     => 'Updated At',
        ],

        'groups' => [
            'name'           => 'Name',
            'status'         => 'Status',
            'decimal-places' => 'Decimal Places',
            'creation-date'  => 'Creation Date',
            'last-update'    => 'Last Update',
        ],

        'filters' => [
            'status' => 'Status',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title'   => 'Currency deleted',
                    'body'    => 'The currency has been deleted successfully.',

                    'success' => [
                        'title' => 'Currency deleted',
                        'body'  => 'The currency has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Currency could not be deleted',
                        'body'  => 'The currency cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Currencies deleted',
                    'body'  => 'The currencies have been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Currency Information',

                'entries' => [
                    'name'         => 'Currency Name',
                    'symbol'       => 'Currency Symbol',
                    'full-name'    => 'Full Name',
                    'iso-numeric'  => 'ISO Numeric Code',
                ],
            ],

            'format-information' => [
                'title' => 'Format Configuration',

                'entries' => [
                    'decimal-places' => 'Decimal Places',
                    'rounding'       => 'Rounding Precision',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Status & Configuration',

                'entries' => [
                    'status' => 'Status',
                ],
            ],

            'rates' => [
                'title'       => 'Currency Rates',

                'entries' => [
                    'name'              => 'Date',
                    'unit-per-currency' => 'Unit Per :currency',
                    'currency-per-unit' => ':currency Per Unit',
                ],
            ],
        ],
    ],
];
