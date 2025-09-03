<?php

return [
    'heading' => [
        'title' => 'Time Off Requests',
    ],

    'modal-actions' => [
        'edit' => [
            'title'                         => 'Edit',
            'duration-display'              => ':count working day|:count working days',
            'duration-display-with-weekend' => ':count working day (+ :weekend weekend day)|:count working days (+ :weekend weekend days)',

            'notification' => [
                'title' => 'Time Off Updated',
                'body'  => 'Your time off request has been updated successfully.',
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

            'success' => [
                'notification' => [
                    'title' => 'Time Off Created',
                    'body'  => 'Your time off request has been created successfully.',
                ],
            ],
        ],
    ],

    'form' => [
        'title'       => 'Time Off Request',
        'description' => 'Create or edit your time off request with the following details:',

        'fields' => [
            'time-off-type'             => 'Time Off Type',
            'time-off-type-placeholder' => 'Select a time off type',
            'time-off-type-helper'      => 'Select the type of time off you are requesting.',
            'request-date-from'         => 'Request Date From',
            'request-date-to'           => 'Request Date To',
            'period'                    => 'Period',
            'half-day'                  => 'Half Day',
            'half-day-helper'           => 'Toggle for half-day leave.',
            'requested-days'            => 'Requested (Days/Hours)',
            'description'               => 'Description',
            'description-placeholder'   => 'No description provided',
            'description-helper'        => 'Provide a brief description of your time off request.',
            'duration'                  => 'Duration',
            'please-select-dates'       => 'Please select the request date from and to.',
        ],
    ],

    'infolist' => [
        'title'       => 'Time Off Details',
        'description' => 'Here are the details of your time off request:',
        'entries'     => [
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
