<?php

return [
    'tabs' => [
        'all'      => 'All Users',
        'archived' => 'Archived Users',
    ],

    'header-actions' => [
        'invite' => [
            'title' => 'Invite User',
            'modal' => [
                'submit-action-label' => 'Invite User',
            ],
            'form' => [
                'email' => 'Email',
            ],
            'notification' => [
                'success' => [
                    'title' => 'User invited',
                    'body'  => 'User has been invited successfully',
                ],
                'error' => [
                    'title' => 'User Invitation Failed',
                    'body'  => 'The system encountered an unexpected error while trying to send the user invitation.',
                ],

                'default-company-error' => [
                    'title' => 'Default Company Not Set',
                    'body'  => 'Please set the default company from settings, before inviting a user.',
                ],
            ],
        ],

        'create' => [
            'label' => 'New User',
        ],
    ],
];
