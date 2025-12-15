<?php

return [
    'setup' => [
        'title'               => 'Followers',
        'submit-action-title' => 'Add Follower',
        'tooltip'             => 'Add Follower',

        'form' => [
            'fields' => [
                'recipients'  => 'Recipients',
                'notify-user' => 'Notify User',
                'add-a-note'  => 'Add a note',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Follower Added',
                    'body'  => '":partner" has been added as a follower.',
                ],

                'error' => [
                    'title' => 'Follower added error',
                    'body'  => 'Failed to ":partner" as follower',
                ],
            ],

            'mail' => [
                'subject' => 'Invitation to follow :model: :department',
            ],
        ],
    ],
];
