<?php

return [
    'title' => 'Manage CRM',

    'actions' => [
        'assign-now' => [
            'label'        => 'Assign now',
            'description'  => 'Assign unassigned leads to sales teams and salespeople now, using the assignment rules.',
            'notification' => 'Assigned :teams lead(s) to sales teams and :salespeople to salespeople.',
        ],
    ],

    'form' => [
        'use-leads' => [
            'label'  => 'Lead Qualification',
            'helper' => 'Add a qualification step before the creation of an opportunity.',
        ],

        'recurring-revenues' => [
            'label'  => 'Recurring Revenue Plans',
            'helper' => 'Define recurring plans and revenues on opportunities.',
        ],

        'multi-teams' => [
            'label'  => 'Salespeople in Multiple Teams',
            'helper' => 'Assign salespersons into multiple Sales Teams.',
        ],

        'auto-assignment' => [
            'label'        => 'Automatic Lead Assignment',
            'helper'       => 'Periodically assign leads based on rules. All sales teams use this setting by default unless specified otherwise.',
            'running'      => 'Running',
            'manual'       => 'Manually',
            'auto'         => 'Repeatedly',
            'repeat-every' => 'Repeat every',
            'unit'         => 'Unit',
            'next-run'     => 'Next run: :date',

            'units' => [
                'minutes' => 'Minutes',
                'hours'   => 'Hours',
                'days'    => 'Days',
                'weeks'   => 'Weeks',
            ],
        ],
    ],
];
