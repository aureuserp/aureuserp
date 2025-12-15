<?php

return [
    'setup' => [
        'title'               => 'Schedule Activity',
        'submit-action-title' => 'Schedule',

        'form' => [
            'fields' => [
                'activity-plan' => 'Activity Plan',
                'plan-date'     => 'Plan Date',
                'plan-summary'  => 'Plan Summary',
                'activity-type' => 'Activity Type',
                'due-date'      => 'Due Date',
                'summary'       => 'Summary',
                'assigned-to'   => 'Assigned To',
                'log-note'      => 'Log note',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Activity Created',
                    'body'  => 'The activity has been created.',
                ],

                'warning'  => [
                    'title' => 'No new files',
                    'body'  => 'All files have already been uploaded.',
                ],

                'error' => [
                    'title' => 'Activity creation failed',
                    'body'  => 'Failed to create activity ',
                ],
            ],
        ],
    ],
];
