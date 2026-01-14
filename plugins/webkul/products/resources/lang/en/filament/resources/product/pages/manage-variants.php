<?php

return [
    'title' => 'Variants',

    'form' => [
        'date'                   => 'Date',
        'employee'               => 'Employee',
        'description'            => 'Description',
        'time-spent'             => 'Time Spent',
        'time-spent-helper-text' => 'Time spent in hours (Eg. 1.5 hours means 1 hour 30 minutes)',
    ],

    'table' => [
        'columns' => [
            'date'                   => 'Date',
            'employee'               => 'Employee',
            'description'            => 'Description',
            'time-spent'             => 'Time Spent',
            'time-spent-on-subtasks' => 'Time Spent on Subtasks',
            'total-time-spent'       => 'Total Time Spent',
            'remaining-time'         => 'Remaining Time',
            'variant-values'         => 'Variant Values',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Variant deleted',
                    'body'  => 'The variant has been deleted successfully.',
                ],
            ],

            'view' => [
                'extra-footer-actions' => [
                    'print' => [
                        'label' => 'Print Labels',

                        'form' => [
                            'fields' => [
                                'quantity' => 'Number of Labels',
                                'format'   => 'Format',

                                'format-options' => [
                                    'dymo'       => 'Dymo',
                                    '2x7_price'  => '2x7 with price',
                                    '4x7_price'  => '4x7 with price',
                                    '4x12'       => '4x12',
                                    '4x12_price' => '4x12 with price',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
