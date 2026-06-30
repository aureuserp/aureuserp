<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Applicant deleted',
                'body'  => 'The applicant has been deleted successfully.',
            ],
        ],

        'refuse' => [
            'title'        => 'Refuse Reason',
            'form'         => [
                'fields' => [
                    'notify'   => 'Notify',
                    'email-to' => 'Email To',
                ],
            ],
            'notification' => [
                'title' => 'Applicant refused',
                'body'  => 'The applicant has been refused successfully.',
            ],
        ],

        'reopen' => [
            'title'        => 'Restore Applicant from refuse',
            'notification' => [
                'title' => 'Applicant reopened',
                'body'  => 'The applicant has been reopened successfully.',
            ],
        ],

        'state' => [
            'notification' => [
                'title' => 'Applicant state updated',
                'body'  => 'The applicant state has been updated successfully.',
            ],
        ],
    ],

    'mail' => [
        'application-refused' => [
            'subject' => 'Your Job Application: :application',
        ],
    ],
];
