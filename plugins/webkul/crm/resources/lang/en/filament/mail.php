<?php

return [
    'alias' => [
        'section'             => 'Email alias',
        'section-description' => 'Emails sent to this address create leads in this team.',
        'name'                => 'Email alias',
        'name-helper'         => 'The part before the @, e.g. "sales".',
        'name-invalid'        => 'Use letters, numbers, dots, dashes or underscores only.',
        'name-taken'          => 'This alias is already used by another sales team.',
        'domain'              => 'Alias domain',
        'domain-helper'       => 'The part after the @, e.g. "yourcompany.com". The mailbox of an incoming mail server must receive emails for it.',
        'domain-invalid'      => 'Enter a domain such as yourcompany.com.',
        'contact'             => 'Accept emails from',
    ],

    'reasons' => [
        'no-sender'   => 'The email has no valid sender address.',
        'auto-reply'  => 'Automatic reply (out of office, bounce or mailing list).',
        'from-alias'  => 'Sent from one of the sales team aliases (mail loop).',
        'no-alias'    => 'Not sent to any sales team alias.',
        'not-allowed' => 'The sender is not allowed by the alias ("Accept emails from": :contact).',
    ],

    'errors' => [
        'no-imap'    => 'The PHP imap extension is not installed on the server.',
        'connection' => 'Could not connect to the mail server.',
    ],

    'chatter' => [
        'from' => 'From:',
    ],

    'no-subject' => 'Email from :email',
];
