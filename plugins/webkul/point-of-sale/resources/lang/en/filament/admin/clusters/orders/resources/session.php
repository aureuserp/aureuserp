<?php

return [

    'navigation' => [
        'title' => 'Sessions',
        'group' => 'Point of Sale',
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Session',
                'entries' => [
                    'session'          => 'Session',
                    'opened-by'        => 'Opened By',
                    'point-of-sale'    => 'Point of Sale',
                    'opening-date'     => 'Opening Date',
                    'starting-balance' => 'Starting Balance',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                  => 'Session ID',
            'config'                => 'Point of Sale',
            'user'                  => 'Opened By',
            'started-at'            => 'Opening Date',
            'stopped-at'            => 'Closing Date',
            'cash-balance-start'    => 'Starting Balance',
            'cash-balance-end-real' => 'Ending Balance',
            'cash-balance-end'      => 'Theoretical Closing Balance',
            'order-count'           => 'Orders',
            'total-payments-amount' => 'Payments',
            'cash-difference'       => 'Difference',
            'is-rescue'             => 'Rescue',
            'has-failed-operations' => 'Failed Operations',
            'state'                 => 'Status',
            'company'               => 'Company',
        ],

        'groups' => [
            'config'     => 'Point of Sale',
            'state'      => 'Status',
            'started-at' => 'Opened At',
        ],

        'filters' => [
            'state'  => 'Status',
            'config' => 'Point of Sale',
        ],
    ],
];
