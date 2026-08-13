<?php

return [
    'heading' => [
        'title' => 'Maintenance Calendar',
    ],

    'config' => [
        'button-text' => [
            'today' => 'Today',
            'year'  => 'Year',
            'month' => 'Month',
            'week'  => 'Week',
            'list'  => 'List',
        ],
    ],

    'header-actions' => [
        'create' => [
            'label'         => 'New Request',
            'modal-heading' => 'New Maintenance Request',
            'notification'  => [
                'success' => [
                    'title' => 'Maintenance request created',
                    'body'  => 'The maintenance request has been created successfully.',
                ],
                'error' => [
                    'title' => 'Maintenance request could not be created',
                    'body'  => 'Create a maintenance stage and team first.',
                ],
            ],
        ],
    ],

    'view-action' => [
        'label' => 'View',
    ],

    'modal-actions' => [
        'edit' => [
            'label' => 'Edit',
        ],
    ],

    'form' => [
        'fields' => [
            'subject'      => 'Subject',
            'scheduled-at' => 'Scheduled At',
        ],
    ],

    'infolist' => [
        'title'   => 'Maintenance Request',
        'entries' => [
            'subject'          => 'Subject',
            'date'             => 'Date',
            'time'             => 'Time',
            'technician'       => 'Technician',
            'priority'         => 'Priority',
            'maintenance-type' => 'Maintenance Type',
            'stage'            => 'Stage',
        ],
    ],
];
