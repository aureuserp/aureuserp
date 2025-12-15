<?php

return [
    'title' => 'Work Locations',

    'navigation' => [
        'title' => 'Work Locations',
        'group' => 'Employee',
    ],

    'form' => [
        'name'            => 'Name',
        'company'         => 'Company',
        'location-type'   => 'Location Type',
        'location-number' => 'Location Number',
        'status'          => 'Status',
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'name'            => 'Name',
            'status'          => 'Status',
            'company'         => 'Company',
            'location-type'   => 'Location Type',
            'location-number' => 'Location Number',
            'deleted-at'      => 'Deleted At',
            'created-by'      => 'Created By',
            'created-at'      => 'Created At',
            'updated-at'      => 'Updated At',
        ],

        'filters' => [
            'name'            => 'Name',
            'status'          => 'Status',
            'created-by'      => 'Created By',
            'company'         => 'Company',
            'location-number' => 'Location Number',
            'location-type'   => 'Location Type',
            'updated-at'      => 'Updated At',
            'created-at'      => 'Created At',
        ],

        'groups' => [
            'name'          => 'Name',
            'status'        => 'Status',
            'location-type' => 'Location Type',
            'company'       => 'Company',
            'created-by'    => 'Created By',
            'created-at'    => 'Created At',
            'updated-at'    => 'Updated At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Work Location updated',
                    'body'  => 'The work Location has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Work Location restored',
                    'body'  => 'The work Location has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Work Location deleted',
                    'body'  => 'The work Location has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Work Location force deleted',
                    'body'  => 'The work Location has been force deleted successfully.',
                ],
            ],

            'empty-state' => [
                'notification' => [
                    'title' => 'Work Location created',
                    'body'  => 'The Work Location has been created successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Work Locations deleted',
                    'body'  => 'The work Locations has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Work Locations force deleted',
                    'body'  => 'The work Locations has been force deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'            => 'Name',
        'company'         => 'Company',
        'location-type'   => 'Location Type',
        'location-number' => 'Location Number',
        'status'          => 'Status',
    ],
];
