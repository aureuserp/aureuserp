<?php

return [
    'title'        => 'Send By Email',
    'resend-title' => 'Re-Send By Email',
    'quotation'    => 'quotation',
    'quotations'   => 'quotations',

    'modal' => [
        'heading' => 'Send Quotation By Email',
    ],

    'form' => [
        'fields' => [
            'partners'    => 'Partners',
            'subject'     => 'Subject',
            'description' => 'Description',
            'attachment'  => 'Attachment',
        ],
    ],

    'actions' => [
        'notification' => [
            'email' => [
                'no_recipients' => [
                    'title' => 'No Recipients Selected',
                    'body'  => 'Please select at least one partner to send quotations to.',
                ],

                'all_success' => [
                    'title' => 'Quotations Sent!',
                    'body'  => 'Your :plural have been successfully delivered to: :recipients',
                ],

                'all_failed' => [
                    'title' => 'Unable to Send Quotations',
                    'body'  => 'We encountered issues sending your quotations: :failures',
                ],

                'partial_success' => [
                    'title'       => 'Some Quotations Sent',
                    'sent_part'   => 'Successfully delivered to: :recipients',
                    'failed_part' => 'Could not deliver to: :failures',
                ],

                'failure_item' => ':partner (:reason)',
            ],
        ],
    ],

];
