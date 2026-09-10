<?php

return [
    'group' => [
        'label' => 'Portal Access',
    ],

    'grant' => [
        'label' => 'Grant Portal Access',

        'modal' => [
            'heading'     => 'Grant Portal Access',
            'description' => 'An invitation email will be sent to :email with a link to set a password for the customer portal.',
        ],

        'notification' => [
            'email-missing' => [
                'title' => 'Email address required',
                'body'  => 'Add an email address to this contact before granting portal access.',
            ],

            'email-taken' => [
                'title' => 'Email address already in use',
                'body'  => 'Another contact with portal access is already using this email address.',
            ],

            'granted' => [
                'title' => 'Portal access granted',
                'body'  => 'An invitation to set a password has been sent to :email.',
            ],

            'email-failed' => [
                'title' => 'Portal access granted, but the invitation email could not be sent',
            ],
        ],
    ],

    'change-password' => [
        'label' => 'Change Password',

        'modal' => [
            'heading'     => 'Change Portal Password',
            'description' => 'Set a new customer portal password for :email.',
        ],

        'form' => [
            'password' => [
                'label' => 'New Password',
            ],

            'password-confirmation' => [
                'label' => 'Confirm New Password',
            ],
        ],

        'notification' => [
            'changed' => [
                'title' => 'Portal password changed',
                'body'  => 'The contact can now sign in to the customer portal with the new password.',
            ],
        ],
    ],

    'password-reset' => [
        'label' => 'Send Password Reset',

        'modal' => [
            'heading'     => 'Send Password Reset',
            'description' => 'A password reset link will be sent to :email.',
        ],

        'notification' => [
            'sent' => [
                'title' => 'Password reset link sent',
                'body'  => 'A password reset link has been sent to :email.',
            ],

            'failed' => [
                'title' => 'Password reset link could not be sent',
            ],
        ],
    ],

    'revoke' => [
        'label' => 'Revoke Portal Access',

        'modal' => [
            'heading'     => 'Revoke Portal Access',
            'description' => 'The contact will no longer be able to sign in to the customer portal. Any pending password reset links will be invalidated.',
        ],

        'notification' => [
            'revoked' => [
                'title' => 'Portal access revoked',
                'body'  => 'The contact can no longer sign in to the customer portal.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'portal-access' => 'Portal Access',
        ],

        'filters' => [
            'portal-access' => 'Portal Access',
        ],
    ],

    'infolist' => [
        'section' => [
            'title' => 'Customer Portal',
        ],

        'entries' => [
            'status' => [
                'label'   => 'Portal Access',
                'granted' => 'Granted',
                'none'    => 'Not Granted',
            ],

            'email-verified-at' => [
                'label'       => 'Email Verified At',
                'placeholder' => 'Never',
            ],

            'last-login-at' => [
                'label'       => 'Last Portal Login',
                'placeholder' => 'Never',
            ],
        ],
    ],
];
