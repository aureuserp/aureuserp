<?php

return [
    'title' => 'Print & Send',

    'modal' => [
        'title' => 'Preview Invoice',

        'form' => [
            'partners'    => 'Customer',
            'subject'     => 'Subject',
            'description' => 'Description',
            'files'       => 'Attachment',
        ],

        'action' => [
            'submit' => [
                'title' => 'Send',
            ],
        ],

        'notification' => [
            'invoice-sent' => [
                'title' => 'Invoice Sent',
                'body'  => 'Invoice has been sent successfully.',
            ],
        ],
    ],
];
