<?php

return [
    'title' => 'Time off',

    'model-label' => 'Time off',

    'navigation' => [
        'title' => 'Time off',
    ],

    'form' => [
        'fields' => [
            'employee-name'     => 'Employee Name',
            'department-name'   => 'Department Name',
            'time-off-type'     => 'Time off Type',
            'date'              => 'Date',
            'dates'             => 'Dates',
            'request-date-from' => 'Request Date From',
            'request-date-to'   => 'Request Date To',
            'description'       => 'Description',
            'period'            => 'Period',
            'half-day'          => 'Half Day',
            'requested-days'    => 'Requested (Days/Hours)',
            'description'       => 'Description',
            'attachment'        => 'Attachment',
            'day'               => ':day day',
            'days'              => ':days day(s)',
        ],
    ],

    'table' => [
        'columns' => [
            'employee-name'  => 'Employee',
            'time-off-type'  => 'Time Off Type',
            'description'    => 'Description',
            'date-from'      => 'Date From',
            'date-to'        => 'Date To',
            'duration'       => 'Duration',
            'status'         => 'Status',
        ],

        'groups' => [
            'employee-name' => 'Employee',
            'time-off-type' => 'Time Off Type',
            'status'        => 'Status',
            'start-date'    => 'Start Date',
            'start-to'      => 'End Date',
            'updated-at'    => 'Updated At',
            'created-at'    => 'Created At',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Validate',
                    'approve'  => 'Approve',
                ],
                'notification' => [
                    'title' => 'Time Off approved',
                    'body'  => 'The time off has been approved successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Time Off deleted',
                    'body'  => 'The time off has been deleted successfully.',
                ],
            ],

            'refused' => [
                'title'        => 'Refuse',
                'notification' => [
                    'title' => 'Time Off refused',
                    'body'  => 'The time off has been refused successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Time Off deleted',
                    'body'  => 'The time off has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'employee-name'     => 'Employee Name',
            'department-name'   => 'Department Name',
            'time-off-type'     => 'Time off Type',
            'date'              => 'Date',
            'dates'             => 'Dates',
            'request-date-from' => 'Request Date From',
            'request-date-to'   => 'Request Date To',
            'description'       => 'Description',
            'period'            => 'Period',
            'half-day'          => 'Half Day',
            'requested-days'    => 'Requested (Days/Hours)',
            'attachment'        => 'Attachment',
            'day'               => ':day day',
            'days'              => ':days day(s)',
            'date-from'         => 'Date From',
            'date-to'           => 'Date To',
            'status'            => 'Status',
        ],
    ],
];
