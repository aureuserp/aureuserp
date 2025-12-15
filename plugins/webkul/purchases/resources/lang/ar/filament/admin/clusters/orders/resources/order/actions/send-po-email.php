<?php

return [
    'label' => 'Send PO By Email',

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
        ],
    ],
];
