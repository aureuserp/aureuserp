<?php

return [
    'placeholders' => [
        'no-record-found' => 'No record found.',
        'loading'         => 'Loading Chatter...',
    ],

    'activity-infolist' => [
        'title' => 'Activities',
    ],

    'cancel-activity-plan-action' => [
        'title' => 'Cancel Activity',
    ],

    'delete-message-action' => [
        'title' => 'Delete Message',
    ],

    'edit-activity' => [
        'title' => 'Edit Activity',

        'form' => [
            'fields' => [
                'activity-plan' => 'Activity Plan',
                'plan-date'     => 'Plan Date',
                'plan-summary'  => 'Plan Summary',
                'activity-type' => 'Activity Type',
                'due-date'      => 'Due Date',
                'summary'       => 'Summary',
                'assigned-to'   => 'Assigned To',
            ],
        ],

        'action' => [
            'notification' => [
                'success' => [
                    'title' => 'Activity updated',
                    'body'  => 'The activity has been updated successfully.',
                ],
            ],
        ],
    ],

    'process-message' => [
        'original-note' => '<br><div><span class="font-bold">Original Note</span>: :body</div>',
        'original-note' => '<br><div><span class="font-bold">Original Note</span>: :body</div>',
        'feedback'      => '<div><span class="font-bold">Feedback</span>: <p>:feedback</p></div>',
    ],

    'mark-as-done' => [
        'title' => 'Mark as done',
        'form'  => [
            'fields' => [
                'feedback' => 'Feedback',
            ],
        ],

        'footer-actions' => [
            'label' => 'Done & Schedule Next',

            'actions' => [
                'notification' => [
                    'mark-as-done' => [
                        'title' => 'Activity mark as done',
                        'body'  => 'The activity mark as done successfully.',
                    ],
                ],
            ],
        ],
    ],
];
