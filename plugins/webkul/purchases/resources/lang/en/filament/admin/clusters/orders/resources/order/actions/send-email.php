<?php

return [
    'label'        => 'Send By Email',
    'resend-label' => 'Re-Send By Email',

    'form' => [
        'fields' => [
            'to'      => 'To',
            'subject' => 'Subject',
            'message' => 'Message',
        ],
    ],

    'action' => [
        'notification' => [
            'success' => [
                'title' => 'Email sent',
                'body'  => 'The email has been sent successfully.',
            ],

            'warning' => [
                'title' => 'Some emails were not sent',
                'body'  => 'Some vendors will not get the email because their email address is not available.',
            ],

            'danger' => [
                'title' => 'Email not sent',
                'body'  => 'Please add an email address to the selected vendors and try again.',
            ],
        ],
    ],
];
