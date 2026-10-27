<?php

return [
    'group' => [
        'label' => 'Actions',
    ],

    'archive' => [
        'label'            => 'Archive',
        'description'      => 'This record will be archived. You can find it again with the archived filter and unarchive it.',
        'bulk-description' => 'The selected records will be archived. You can find them again with the archived filter and unarchive them.',
        'notification'     => ':count record(s) archived.',
    ],

    'unarchive' => [
        'label' => 'Unarchive',
    ],

    'delete' => [
        'label' => 'Delete',
    ],

    'duplicate' => [
        'label'        => 'Duplicate',
        'copy'         => ':name (copy)',
        'notification' => ':count record(s) duplicated.',
    ],

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
