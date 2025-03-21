<?php

return [
    'modal-actions' => [
        'edit' => [
            'title' => 'Edit',
        ],

        'delete' => [
            'title' => 'Delete',
        ],
    ],

    'view-action' => [
        'title'       => 'View',
        'description' => 'View Time Off Request',
    ],

    'header-actions' => [
        'create' => [
            'title'       => 'New Time Off',
            'description' => 'Create Time Off Request',

            'employee-not-found' => [
                'notification' => [
                    'title' => 'Employee Not Found',
                    'body'  => 'Please add an employee to your profile before creating a time off request.',
                ],
            ],
        ],
    ],

    'form' => [
        'fields' => [
            'time-off-type'     => 'Time Off Type',
            'request-date-from' => 'Request Date From',
            'request-date-to'   => 'Request Date To',
            'period'            => 'Period',
            'half-day'          => 'Half Day',
            'requested-days'    => 'Requested (Days/Hours)',
            'description'       => 'Description',
        ],
    ],

    'infolist' => [
        'entries' => [
            'time-off-type'           => 'Time Off Type',
            'request-date-from'       => 'Request Date From',
            'request-date-to'         => 'Request Date To',
            'description'             => 'Description',
            'description-placeholder' => 'No description provided',
            'duration'                => 'Duration',
            'status'                  => 'Status',
        ],
    ],

    'events' => [
        'title' => ':name On :status: :days day(s)',
    ],
];
