<?php

return [
    'title' => 'Timesheets',

    'navigation' => [
        'title' => 'Timesheets',
        'group' => 'Project',
    ],

    'form' => [
        'date'                   => 'Date',
        'employee'               => 'Employee',
        'project'                => 'Project',
        'task'                   => 'Task',
        'description'            => 'Description',
        'time-spent'             => 'Time Spent',
        'time-spent-helper-text' => 'Time spent in hours (Eg. 1.5 hours means 1 hour 30 minutes)',
    ],

    'table' => [
        'columns' => [
            'date'        => 'Date',
            'employee'    => 'Employee',
            'project'     => 'Project',
            'task'        => 'Task',
            'description' => 'Description',
            'time-spent'  => 'Time Spent',
            'created-at'  => 'Created At',
            'updated-at'  => 'Updated At',
        ],

        'groups' => [
            'date'       => 'Date',
            'employee'   => 'Employee',
            'project'    => 'Project',
            'task'       => 'Task',
            'creator'    => 'Creator',
        ],

        'filters' => [
            'date-from'  => 'Date From',
            'date-until' => 'Date Until',
            'employee'   => 'Employee',
            'project'    => 'Project',
            'task'       => 'Task',
            'creator'    => 'Creator',
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

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Timesheets deleted',
                    'body'  => 'The timesheets has been deleted successfully.',
                ],
            ],
        ],
    ],
];
