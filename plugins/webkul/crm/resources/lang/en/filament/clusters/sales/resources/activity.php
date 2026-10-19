<?php

return [
    'model-label'        => 'Activity',
    'plural-model-label' => 'Activities',

    'navigation' => [
        'title' => 'My Activities',
    ],

    'views' => [
        'list'   => 'List',
        'kanban' => 'Kanban',
        'graph'  => 'Graph',
    ],

    'table' => [
        'columns' => [
            'due-date'      => 'Due Date',
            'opportunity'   => 'Lead / Opportunity',
            'customer'      => 'Customer',
            'activity-type' => 'Activity Type',
            'summary'       => 'Summary',
            'assigned-to'   => 'Assigned To',
            'stage'         => 'Stage',
            'created-at'    => 'Created At',
        ],

        'groups' => [
            'activity-type' => 'Activity Type',
            'assigned-to'   => 'Assigned To',
            'due-date'      => 'Due Date',
        ],

        'filters' => [
            'due-status'    => 'Due',
            'activity-type' => 'Activity Type',
            'assigned-to'   => 'Assigned To',
        ],

        'empty-state' => [
            'heading'     => 'No activities',
            'description' => 'Use Schedule Activity, or the clock icon on an opportunity, to plan your next step.',
        ],
    ],

    'actions' => [
        'schedule' => [
            'label' => 'Schedule Activity',

            'modal' => [
                'heading' => 'Schedule activity',
                'submit'  => 'Schedule',
            ],

            'form' => [
                'opportunity'   => 'Opportunity',
                'activity-type' => 'Activity Type',
                'due-date'      => 'Due Date',
                'summary'       => 'Summary',
                'assigned-to'   => 'Assigned To',
                'note'          => 'Note',
            ],

            'notification' => [
                'success' => [
                    'title' => 'Activity scheduled',
                    'body'  => 'The activity has been scheduled on ":opportunity".',
                ],

                'forbidden' => [
                    'title' => 'Not allowed',
                    'body'  => 'You do not have permission to schedule activities on this opportunity.',
                ],
            ],
        ],

        'open' => [
            'label' => 'Open Record',
        ],

        'done' => [
            'label' => 'Mark as Done',

            'modal' => [
                'heading' => 'Mark activity as done',
                'submit'  => 'Done',
            ],

            'form' => [
                'feedback' => 'Feedback',
            ],

            'notification' => [
                'title' => 'Activity done',
                'body'  => 'The activity has been marked as done and logged on the opportunity.',
            ],
        ],

        'reschedule' => [
            'label' => 'Reschedule',

            'modal' => [
                'heading' => 'Reschedule activity',
            ],

            'form' => [
                'due-date'    => 'Due Date',
                'summary'     => 'Summary',
                'assigned-to' => 'Assigned To',
            ],

            'notification' => [
                'title' => 'Activity rescheduled',
                'body'  => 'The activity has been updated.',
            ],
        ],

        'cancel' => [
            'label'      => 'Cancel Activity',
            'bulk-label' => 'Cancel Activities',

            'modal' => [
                'heading' => 'Cancel activity',
            ],

            'notification' => [
                'title' => 'Activity cancelled',
                'body'  => 'The activity has been removed.',
            ],
        ],
    ],
];
