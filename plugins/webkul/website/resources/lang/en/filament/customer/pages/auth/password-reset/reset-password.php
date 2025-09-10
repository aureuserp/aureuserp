<?php

return [
    'title'         => 'Reset password',
    'heading'       => 'Reset password',
    'notifications' => [
        'throttled' => [
            'title' => 'Too many attempts. Try again in :seconds seconds.',
            'body'  => 'Please wait :seconds seconds (:minutes minutes) before trying again.',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'Email address',
        ],
        'password' => [
            'label'                => 'New password',
            'validation_attribute' => 'password',
        ],
        'password_confirmation' => [
            'label' => 'Confirm new password',
        ],
        'actions' => [
            'reset' => [
                'label' => 'Reset password',
            ],
        ],
    ],
];
