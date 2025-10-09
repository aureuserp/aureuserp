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
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Variant deleted',
                    'body'  => 'The variant has been deleted successfully.',
                ],
            ],
        ],
    ],
];
