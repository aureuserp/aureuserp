<?php

return [
    'navigation' => [
        'title' => 'Work Orders',
        'group' => 'Operations',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'work-order'          => 'Work Order',
                    'work-center'         => 'Work Center',
                    'product'             => 'Product',
                    'quantity'            => 'Quantity',
                    'manufacturing-order' => 'Manufacturing Order',
                    'lot-serial'          => 'Lot/Serial Number',
                    'start-date'          => 'Start Date',
                    'end-date'            => 'End Date',
                    'date-range-separator'=> 'to',
                    'expected-duration'   => 'Expected Duration',
                    'duration-suffix'     => 'minutes',
                    'real-duration'       => 'Real Duration',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'      => 'Time Tracking',
                'add-action' => 'Add a line',
                'columns'    => [
                    'user'         => 'User',
                    'duration'     => 'Duration',
                    'start-date'   => 'Start Date',
                    'end-date'     => 'End Date',
                    'productivity' => 'Productivity',
                ],
                'footer' => [
                    'real-duration' => 'Real Duration',
                ],
            ],
            'components' => [
                'title'      => 'Components',
                'add-action' => 'Add a line',
                'columns'    => [
                    'product'    => 'Product',
                    'from'       => 'From',
                    'to-consume' => 'To Consume',
                    'quantity'   => 'Quantity',
                    'uom'        => 'UoM',
                ],
            ],
            'work-instruction' => [
                'title'   => 'Work Instruction',
                'entries' => [
                    'operation' => 'Operation',
                    'worksheet' => 'Worksheet',
                ],
            ],
            'blocked-by' => [
                'title'  => 'Blocked By',
                'fields' => [
                    'work-orders' => 'Work Orders',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'operation'           => 'Operation',
            'work-center'         => 'Work Center',
            'manufacturing-order' => 'Manufacturing Order',
            'product'             => 'Product',
            'quantity-remaining'  => 'Quantity Remaining',
            'lot-serial'          => 'Lot/Serial',
            'start'               => 'Start',
            'end'                 => 'End',
            'expected-duration'   => 'Expected Duration',
            'real-duration'       => 'Real Duration',
            'status'              => 'Status',
        ],
        'groups' => [
            'status'              => 'Status',
            'work-center'         => 'Work Center',
            'manufacturing-order' => 'Manufacturing Order',
            'product'             => 'Product',
            'start'               => 'Start',
            'end'                 => 'End',
        ],
        'filters' => [
            'work-order'          => 'Work Order',
            'status'              => 'Status',
            'operation'           => 'Operation',
            'work-center'         => 'Work Center',
            'manufacturing-order' => 'Manufacturing Order',
            'product'             => 'Product',
            'start'               => 'Start',
            'end'                 => 'End',
            'created-at'          => 'Created At',
            'updated-at'          => 'Updated At',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'work-order'          => 'Work Order',
                    'work-center'         => 'Work Center',
                    'product'             => 'Product',
                    'quantity'            => 'Quantity',
                    'manufacturing-order' => 'Manufacturing Order',
                    'lot-serial'          => 'Lot/Serial Number',
                    'start-date'          => 'Start Date',
                    'end-date'            => 'End Date',
                    'expected-duration'   => 'Expected Duration',
                    'real-duration'       => 'Real Duration',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'  => 'Time Tracking',
                'footer' => [
                    'real-duration' => 'Real Duration',
                ],
            ],
            'components' => [
                'title' => 'Components',
            ],
            'work-instruction' => [
                'title'   => 'Work Instruction',
                'entries' => [
                    'operation' => 'Operation',
                    'worksheet' => 'Worksheet',
                ],
            ],
            'blocked-by' => [
                'title'   => 'Blocked By',
                'columns' => [
                    'work-order'  => 'Work Order',
                    'work-center' => 'Work Center',
                    'status'      => 'Status',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label' => 'New Work Order',
                ],
            ],
        ],
    ],
];
