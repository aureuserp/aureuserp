<?php

return [
    'model-label'        => 'Incoming Mail Server',
    'plural-model-label' => 'Incoming Mail Servers',

    'navigation' => [
        'title' => 'Incoming Mail Servers',
        'group' => 'Email',
    ],

    'header-actions' => [
        'create' => [
            'label'        => 'New Mail Server',
            'notification' => 'Mail server created',
        ],
    ],

    'form' => [
        'name'                        => 'Name',
        'name-placeholder'            => 'e.g. Sales mailbox',
        'host'                        => 'Server (IMAP host)',
        'encryption'                  => 'Encryption',
        'port'                        => 'Port',
        'folder'                      => 'Folder',
        'username'                    => 'Username',
        'password'                    => 'Password',
        'password-helper'             => 'Leave empty to keep the current password.',
        'validate-certificate'        => 'Validate SSL certificate',
        'validate-certificate-helper' => 'Turn off only for servers with a self-signed certificate.',
        'active'                      => 'Active',
        'active-helper'               => 'Read every minute for new emails.',

        'encryptions' => [
            'ssl'  => 'SSL/TLS (port 993)',
            'tls'  => 'STARTTLS (port 143)',
            'none' => 'None (port 143)',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Name',
            'host'         => 'Server',
            'folder'       => 'Folder',
            'active'       => 'Active',
            'last-fetched' => 'Last checked',
            'last-error'   => 'Last error',
        ],

        'actions' => [
            'test' => [
                'label'   => 'Test connection',
                'success' => 'Connection successful: :count email(s) in the folder.',
                'failure' => 'Connection failed',
            ],

            'fetch' => [
                'label'        => 'Fetch now',
                'queued'       => 'Fetching emails',
                'queued-body'  => 'New emails are read in the background (the queue worker must be running). See Received Emails for the result.',
            ],

            'edit' => [
                'notification' => 'Mail server updated',
            ],

            'delete' => [
                'notification' => 'Mail server deleted',
            ],
        ],

        'empty' => [
            'heading'     => 'No incoming mail server',
            'description' => 'Add the mailbox that receives the emails sent to your sales team aliases.',
        ],
    ],
];
