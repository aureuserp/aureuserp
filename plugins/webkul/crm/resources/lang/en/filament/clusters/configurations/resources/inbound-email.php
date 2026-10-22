<?php

return [
    'model-label'        => 'Received Email',
    'plural-model-label' => 'Received Emails',

    'navigation' => [
        'title' => 'Received Emails',
    ],

    'table' => [
        'columns' => [
            'received-at' => 'Received',
            'from'        => 'From',
            'subject'     => 'Subject',
            'status'      => 'Result',
            'team'        => 'Sales team',
            'lead'        => 'Lead / Opportunity',
            'reason'      => 'Reason',
            'recipients'  => 'Recipients',
            'server'      => 'Mail server',
        ],

        'empty' => [
            'heading'     => 'No email received yet',
            'description' => 'Emails read from the incoming mail servers are listed here with what was done with them.',
        ],
    ],
];
