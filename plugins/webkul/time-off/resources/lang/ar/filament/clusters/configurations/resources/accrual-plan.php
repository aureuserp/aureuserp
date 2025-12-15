<?php

return [
    'title'      => 'Accrual Plan',
    'navigation' => [
        'title' => 'Accrual Plan',
    ],

    'form' => [
        'fields' => [
            'name'                    => 'Title',
            'is-based-on-worked-time' => 'Is Based on Worked Time',
            'accrued-gain-time'       => 'Accrued Gain Time',
            'carry-over-time'         => 'Carry Over Time',
            'carry-over-date'         => 'Carry Over Date',
            'status'                  => 'Status',
        ],
    ],

    'table' => [
        'columns' => [
            'name'   => 'Name',
            'levels' => 'Levels',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Accrual Plan deleted',
                    'body'  => 'The Accrual Plan has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Accrual Plan deleted',
                    'body'  => 'The Accrual Plan has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'                    => 'Name',
            'is-based-on-worked-time' => 'Is Based on Worked Time',
            'accrued-gain-time'       => 'Accrued Gain Time',
            'carry-over-time'         => 'Carry Over Time',
            'carry-over-day'          => 'Carry Over Day',
            'carry-over-month'        => 'Carry Over Month',
        ],
    ],
];
