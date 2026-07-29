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

    'actions' => [
        'notification' => [
            'email' => [
                'no_recipients' => [
                    'title' => 'No Recipients Selected',
                    'body'  => 'Please select at least one vendor to send purchase order to.',
                ],

                'all_success' => [
                    'title' => 'purchase order Sent!',
                    'body'  => 'Your purchase order have been successfully delivered to: :recipients',
                ],

                'all_failed' => [
                    'title' => 'Unable to Send purchase order',
                    'body'  => 'We encountered issues sending your purchase order: :failures',
                ],

                'partial_success' => [
                    'title'       => 'Some purchase order Sent',
                    'sent_part'   => 'Successfully delivered to: :recipients',
                    'failed_part' => 'Could not deliver to: :failures',
                ],

                'failure_item' => ':partner (:reason)',
            ],
        ],
    ],
];
