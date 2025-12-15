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
            'notification' => [
                'title' => 'Applicant refused',
                'body'  => 'The applicant has been refused successfully.',
            ],
        ],

        'reopen' => [
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
