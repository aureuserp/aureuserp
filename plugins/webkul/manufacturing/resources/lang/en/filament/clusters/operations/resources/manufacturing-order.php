<?php

return [
    'navigation' => [
        'title' => 'Manufacturing Orders',
        'group' => 'Operations',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'product'                => 'Product',
                    'quantity'               => 'Quantity',
                    'uom'                    => 'UoM',
                    'bill-of-material'       => 'Bill of Material',
                    'scheduled-date'         => 'Scheduled Date',
                    'scheduled-end'          => 'Scheduled End',
                    'responsible'            => 'Responsible',
                    'to-produce'             => 'To Produce',
                    'to-produce-placeholder' => 'Image preview',
                    'uom-placeholder'        => 'UoM',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Components',
                'add-action'   => 'Add a line',
                'process-note' => 'Components will be generated as the manufacturing process is built.',
                'columns'      => [
                    'component'          => 'Product',
                    'from'               => 'From',
                    'to-consume'         => 'To Consume',
                    'to-consume-tooltip' => 'Insufficient quantity available',
                    'quantity'           => 'Quantity',
                    'uom'                => 'UoM',
                    'forecast'           => 'Forecast',
                ],
            ],
            'work-orders' => [
                'title'        => 'Work Orders',
                'add-action'   => 'Add a line',
                'process-note' => 'Work orders will be generated after the manufacturing process is configured.',
                'columns'      => [
                    'operation'          => 'Operation',
                    'work-center'        => 'Work Center',
                    'product'            => 'Product',
                    'quantity-remaining' => 'Quantity Remaining',
                    'quantity-produced'  => 'Quantity Produced',
                    'start'              => 'Start',
                    'end'                => 'End',
                    'expected-duration'  => 'Expected Duration',
                    'real-duration'      => 'Real Duration',
                    'status'             => 'Status',
                    'lot-serial'         => 'Lot/Serial',
                ],
                'actions'      => [
                    'open-work-order' => [
                        'tooltip' => 'Open work order',
                    ],

                    'done' => [
                        'label' => 'Done',
                    ],
                ],
            ],
            'by-products' => [
                'title'        => 'By-Products',
                'process-note' => 'By-products will be generated as the manufacturing process is built.',
                'columns'      => [
                    'product'    => 'Product',
                    'to'         => 'To',
                    'to-produce' => 'To Produce',
                    'uom'        => 'UoM',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Miscellaneous',
                'fields' => [
                    'operation-type'             => 'Operation Type',
                    'source'                     => 'Source',
                    'finished-products-location' => 'Finished Products Location',
                    'company'                    => 'Company',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'              => 'Reference',
            'start'                  => 'Start',
            'end'                    => 'End',
            'deadline'               => 'Deadline',
            'product'                => 'Product',
            'lot-serial-number'      => 'Lot/Serial Number',
            'bill-of-material'       => 'Bill of Material',
            'source'                 => 'Source',
            'responsible'            => 'Responsible',
            'mo-readiness'           => 'MO Readiness',
            'component-status'       => 'Component Status',
            'quantity'               => 'Quantity',
            'uom'                    => 'UoM',
            'consumption-efficiency' => 'Consumption Efficiency',
            'expected-duration'      => 'Expected Duration',
            'real-duration'          => 'Real Duration',
            'company'                => 'Company',
            'state'                  => 'State',
        ],
        'groups' => [
            'state'            => 'State',
            'product'          => 'Product',
            'bill-of-material' => 'Bill of Material',
            'responsible'      => 'Responsible',
            'deadline'         => 'Deadline',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'General',
                'entries' => [
                    'product'                  => 'Product',
                    'scheduled-date'           => 'Scheduled Date',
                    'responsible'              => 'Responsible',
                    'quantity'                 => 'Quantity',
                    'uom'                      => 'UoM',
                    'bill-of-material'         => 'Bill of Material',
                    'operation-type'           => 'Operation Type',
                    'consumption-efficiency'   => 'Consumption Efficiency',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Components',
                'process-note' => 'Components will be available after the manufacturing process is configured.',
                'columns'      => [
                    'component' => 'Component',
                    'quantity'  => 'Quantity',
                    'uom'       => 'UoM',
                ],
            ],
            'work-orders' => [
                'title'        => 'Work Orders',
                'process-note' => 'Work orders will be available after the manufacturing process is configured.',
                'columns'      => [
                    'operation'          => 'Operation',
                    'work-center'        => 'Work Center',
                    'product'            => 'Product',
                    'quantity-remaining' => 'Quantity Remaining',
                    'expected-duration'  => 'Expected Duration',
                    'real-duration'      => 'Real Duration',
                    'lot-serial'         => 'Lot/Serial',
                    'start'              => 'Start',
                    'end'                => 'End',
                ],
            ],
            'by-products' => [
                'title'        => 'By-Products',
                'process-note' => 'By-products will be available after the manufacturing process is configured.',
                'columns'      => [
                    'product'    => 'Product',
                    'to'         => 'To',
                    'to-produce' => 'To Produce',
                    'uom'        => 'UoM',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Miscellaneous',
                'entries' => [
                    'operation-type'             => 'Operation Type',
                    'source'                     => 'Source',
                    'finished-products-location' => 'Finished Products Location',
                    'company'                    => 'Company',
                ],
            ],
        ],
    ],

    'pages' => [
        'shared' => [
            'header-actions' => [
                'confirm' => [
                    'label'        => 'Confirm',
                    'notification' => [
                        'title' => 'Manufacturing order confirmed',
                    ],
                ],

                'cancel' => [
                    'label'        => 'Cancel',
                    'notification' => [
                        'title' => 'Manufacturing order canceled',
                    ],
                ],
            ],
        ],
    ],
];
