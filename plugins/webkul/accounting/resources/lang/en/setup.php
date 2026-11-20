<?php

return [
    'action'       => 'Set up accounting for this company',
    'notice'       => 'Accounting is not set up for this company yet. Use \'Set up accounting\' below to create its chart of accounts, journals and default settings.',
    'results'      => [
        'failed' => [
            'title' => 'Accounting setup failed',
            'body'  => 'An unexpected error stopped the setup and nothing was created for this company. Try again, or contact your administrator. Technical details were logged.',
        ],

        'no-company' => [
            'title' => 'No company selected',
            'body'  => 'Select the company to set up accounting for, then try again.',
        ],
    ],
];
