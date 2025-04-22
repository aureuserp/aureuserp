<?php

return [
    'notification' => [
        'title' => 'User updated',
        'body'  => 'The user has been updated successfully.',
    ],

    'header-actions' => [
        'change-password' => [
            'label' => 'Change Password',

            'notification' => [
                'title' => 'Password changed',
                'body'  => 'The password has been changed successfully.',
            ],

            'form' => [
                'new-password'         => 'New Password',
                'confirm-new-password' => 'Confirm New Password',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'User deleted',
                'body'  => 'The user has been deleted successfully.',
            ],
        ],
    ],
];
