<?php

return [
    'title' => 'Outgoing Mail Server',

    'actions' => [
        'test' => [
            'label'   => 'Send Test Email',
            'subject' => 'Test email',
            'body'    => '<p>Your outgoing mail server works.</p>',
            'sent'    => 'Test email sent to :email.',
            'failed'  => 'The test email could not be sent.',
        ],
    ],

    'form' => [
        'active'              => 'Use a custom outgoing mail server',
        'active-helper'       => 'Emails sent from the ERP (for example CRM "Send email") go through this server. When off, the server from the application configuration is used.',
        'host'                => 'SMTP Server',
        'encryption'          => 'Encryption',
        'port'                => 'Port',
        'username'            => 'Username',
        'password'            => 'Password',
        'password-helper'     => 'Leave empty to keep the current password.',
        'from-address'        => 'From Address',
        'from-address-helper' => 'Address the emails are sent from. Empty = the application default.',
        'from-name'           => 'From Name',

        'encryptions' => [
            'tls'  => 'STARTTLS (port 587)',
            'ssl'  => 'SSL/TLS (port 465)',
            'none' => 'None (port 25)',
        ],
    ],
];
