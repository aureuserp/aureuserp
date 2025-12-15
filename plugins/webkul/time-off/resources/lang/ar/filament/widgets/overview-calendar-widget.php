<?php

return [
    'heading' => [
        'title' => 'Time Off Overview',
    ],

    'modal-actions' => [
        'edit' => [
            'title' => 'Edit',
            'notification' => [
                'title' => 'Time Off Updated',
                'body'  => 'Time off request has been updated successfully.',
            ],
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

            'notification' => [
                'title' => 'Time Off Created',
                'body'  => 'Time off request has been created successfully.',
            ],

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
];
