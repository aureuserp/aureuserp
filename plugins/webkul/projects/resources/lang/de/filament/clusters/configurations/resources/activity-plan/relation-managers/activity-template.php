<?php

return [
    'form' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Activity Details',

                'fields' => [
                    'activity-type' => 'Activity Type',
                    'summary'       => 'Summary',
                    'note'          => 'Note',
                ],
            ],

            'assignment' => [
                'title' => 'Assignment',

                'fields' => [
                    'assignment' => 'Assignment',
                    'assignee'   => 'Assignee',
                ],
            ],

            'delay-information' => [
                'title' => 'Delay Information',

                'fields' => [
                    'delay-count'            => 'Delay Count',
                    'delay-unit'             => 'Delay Unit',
                    'delay-from'             => 'Delay From',
                    'delay-from-helper-text' => 'Source of delay calculation',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'activity-type' => 'Activity Type',
            'summary'       => 'Summary',
            'assignment'    => 'Assignment',
            'assigned-to'   => 'Assigned To',
            'interval'      => 'Interval',
            'delay-unit'    => 'Delay Unit',
            'delay-from'    => 'Delay From',
            'created-by'    => 'Created By',
            'created-at'    => 'Created At',
            'updated-at'    => 'Updated At',
        ],

        'groups' => [
            'activity-type' => 'Activity Type',
            'assignment'    => 'Assignment',
            'assigned-to'   => 'Assigned To',
            'interval'      => 'Interval',
            'delay-unit'    => 'Delay Unit',
            'delay-from'    => 'Delay From',
            'created-by'    => 'Created By',
            'created-at'    => 'Created At',
            'updated-at'    => 'Updated At',
        ],

        'filters' => [
            'activity-type'   => 'Activity Type',
            'activity-status' => 'Activity Status',
            'has-delay'       => 'Has Delay',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Activity template updated',
                    'body'  => 'The activity template has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Activity template deleted',
                    'body'  => 'The activity template has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Activity templates deleted',
                    'body'  => 'The activity templates has been deleted successfully.',
                ],
            ],
        ],
    ],
];
