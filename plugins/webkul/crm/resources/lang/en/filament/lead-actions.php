<?php

return [
    'mark-lost' => [
        'label'        => 'Mark Lost',
        'notification' => ':count record(s) marked as lost.',
    ],

    'send-email' => [
        'label'        => 'Send email',
        'short-label'  => 'Email',
        'submit'       => 'Send',

        'form' => [
            'recipient' => 'To',
            'subject'   => 'Subject',
            'body'      => 'Message',
        ],

        'sent'         => 'Email sent to :email.',
        'failed'       => 'The email could not be sent.',
        'notification' => ':sent email(s) sent, :skipped record(s) skipped (no email address or no access).',
    ],

    'send-sms' => [
        'label'        => 'Send SMS',
        'short-label'  => 'SMS',
        'submit'       => 'Send SMS',

        'form' => [
            'recipient' => 'Recipient',
            'message'   => 'Message',
        ],

        'log-subject'  => 'SMS to :number',
        'sent'         => 'SMS sent to :number.',
        'failed'       => 'The SMS could not be sent.',
        'notification' => ':sent SMS sent, :skipped record(s) skipped (no valid number or no access).',
    ],
];
