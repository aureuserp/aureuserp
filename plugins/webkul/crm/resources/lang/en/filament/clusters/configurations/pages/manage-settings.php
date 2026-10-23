<?php

return [
    'navigation' => [
        'title' => 'Settings',
    ],

    'title' => 'Settings',

    'actions' => [
        'assign-now' => [
            'label'        => 'Assign now',
            'description'  => 'Assign unassigned leads to sales teams and salespeople now, using the assignment rules.',
            'notification' => 'Assigned :teams lead(s) to sales teams and :salespeople to salespeople.',
        ],
    ],

    'form' => [
        'scoring' => [
            'section'            => 'Predictive Lead Scoring',
            'description'        => 'The probability of winning each open lead and opportunity is computed from your won and lost history. Stage and sales team are always used.',
            'variables'          => 'Also consider',
            'variables-helper'   => 'Which details of a record the probability takes into account.',
            'start-date'         => 'Consider leads created as of the',
            'start-date-helper'  => 'Leave empty to use the whole history.',
            'update'             => 'Update Probabilities',
            'update-description' => 'Saves these choices, relearns from the won and lost history and recomputes every open record.',
            'updated'            => 'Probabilities updated for :count open record(s).',
        ],

        'sections' => [
            'crm' => 'CRM',
        ],

        'use-leads' => [

            'label'  => 'Leads',

            'helper' => 'Add a qualification step before the creation of an opportunity.',

        ],

        'recurring-revenues' => [

            'label'  => 'Recurring Revenues',

            'helper' => 'Define recurring plans and revenues on opportunities.',

        ],

        'multi-teams' => [
            'label'  => 'Multi Teams',
            'helper' => 'Assign salespersons into multiple Sales Teams.',
        ],

        'auto-assignment' => [
            'label'        => 'Rule-Based Assignment',
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
