<?php

return [
    'rules' => [
        'title'         => 'Assignment rules',
        'match'         => 'Match',
        'match-all'     => 'All of the following rules',
        'match-any'     => 'Any of the following rules',
        'conditions'    => 'Conditions',
        'field'         => 'Field',
        'operator'      => 'Operator',
        'value'         => 'Value',
        'add-condition' => 'Add condition',
        'matching'      => '{1} :count open record matches these rules|[0,*] :count open records match these rules',
        'no-conditions' => 'No conditions: every lead is accepted.',

        'fields' => [
            'type'                  => 'Type',
            'name'                  => 'Name',
            'contact_name'          => 'Contact name',
            'company_name'          => 'Company name',
            'job_title'             => 'Job position',
            'email'                 => 'Email',
            'phone'                 => 'Phone',
            'website'               => 'Website',
            'street1'               => 'Street',
            'city'                  => 'City',
            'zip'                   => 'ZIP',
            'state_id'              => 'State',
            'country_id'            => 'Country',
            'stage_id'              => 'Stage',
            'tag_ids'               => 'Tags',
            'priority'              => 'Priority',
            'probability'           => 'Probability (%)',
            'expected_revenue'      => 'Expected revenue',
            'expected_closing_date' => 'Expected closing',
            'created_at'            => 'Created on',
            'utm_campaign_id'       => 'Campaign',
            'utm_medium_id'         => 'Medium',
            'utm_source_id'         => 'Source',
            'referred_by'           => 'Referred by',
        ],

        'operators' => [
            'contains'     => 'contains',
            'not_contains' => 'does not contain',
            '='            => 'is equal to',
            '!='           => 'is not equal to',
            'starts_with'  => 'starts with',
            'set'          => 'is set',
            'not_set'      => 'is not set',
            '>'            => 'greater than',
            '>='           => 'greater or equal to',
            '<'            => 'lower than',
            '<='           => 'lower or equal to',
            'in'           => 'is in',
            'not_in'       => 'is not in',

            'date' => [
                '>='      => 'is on or after',
                '<='      => 'is on or before',
                'set'     => 'is set',
                'not_set' => 'is not set',
            ],
        ],
    ],

    'team' => [
        'company-helper'         => 'Companies switched on in the top bar. To create a team for another company, switch to it first.',
        'company-not-active'     => 'This company is not active. Switch to it in the top bar first.',
        'section'                => 'Assignment Rules',
        'section-description'    => 'Which leads rule-based assignment gives this team.',
        'capacity'               => ':assigned leads assigned in the last 30 days on a maximum of :max.',
        'use-leads'              => 'Leads',
        'use-opportunities'      => 'Opportunities',
        'skip-assignment'        => 'Skip auto assignment',
        'skip-assignment-helper' => 'Leads are not assigned to this team automatically.',

        'assign-leads' => [
            'label'        => 'Assign Leads',
            'description'  => 'Assign this team\'s unassigned leads to its members now, using the assignment rules.',
            'notification' => 'Assigned :count lead(s) to salespeople.',
        ],
    ],

    'members' => [
        'new-salesperson' => [
            'heading' => 'New salesperson',
        ],

        'title'       => 'Members',
        'description' => 'The salespeople of this team, how many leads each can receive in 30 days, and which leads they accept.',
        'empty'       => 'No members yet',

        'fields' => [
            'salesperson'       => 'Salesperson',
            'capacity'          => 'Max leads (30 days)',
            'capacity-helper'   => 'Maximum number of leads assigned to this salesperson in 30 days.',
            'capacity-assigned' => ':count assigned in the last 30 days.',
            'paused'            => 'Pause assignment',
            'paused-helper'     => 'This salesperson receives no new leads automatically.',
            'active'            => 'Active',
        ],

        'columns' => [
            'salesperson' => 'Salesperson',
            'capacity'    => 'Max leads (30 days)',
            'last-month'  => 'Assigned (30 days)',
            'rules'       => 'Rules',
            'paused'      => 'Paused',
            'active'      => 'Active',
        ],

        'actions' => [
            'add'    => 'Add member',
            'remove' => 'Remove',
        ],

        'notifications' => [
            'added'        => 'Member added',
            'removed-from' => 'Removed from: :teams (Multi Teams is off).',

            'single-team' => [
                'title' => 'Salespeople can only be in one team',
            ],
        ],
    ],
];
