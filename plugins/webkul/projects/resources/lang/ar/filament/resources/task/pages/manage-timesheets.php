<?php

return [
    'title' => 'Timesheets',

    'form' => [
        'date'                   => 'Date',
        'employee'               => 'Employee',
        'description'            => 'Description',
        'time-spent'             => 'Time Spent',
        'time-spent-helper-text' => 'Time spent in hours (Eg. 1.5 hours means 1 hour 30 minutes)',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Add Timesheet',

                'notification' => [
                    'title' => 'Timesheet created',
                    'body'  => 'The timesheet has been created successfully.',
                ],
            ],
        ],

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
            'edit' => [
                'notification' => [
                    'title' => 'Timesheet updated',
                    'body'  => 'The timesheet has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Timesheet deleted',
                    'body'  => 'The timesheet has been deleted successfully.',
                ],
            ],
        ],
    ],
];
