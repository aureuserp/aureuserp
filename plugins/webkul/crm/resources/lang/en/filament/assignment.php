<?php

return [
    'rules' => [
        'title'         => 'Assignment rules',
        'conditions'    => 'Conditions',
        'field'         => 'Field',
        'operator'      => 'Operator',
        'value'         => 'Value',
        'add-condition' => 'New Rule',

        'heading-all' => 'Match all of the following rules:',
        'heading-any' => 'Match any of the following rules:',
        'records'     => '{0} No matching records|{1} :count record|[2,*] :count records',
        'breakdown'   => ':leads leads · :opportunities opportunities',

        'records-modal' => [
            'heading'     => 'Matching records',
            'description' => 'Open leads and opportunities that are not assigned yet and match these rules. Records already in a team (or, for a member, already with a salesperson) are left out.',
            'empty'       => 'No open lead or opportunity matches these rules.',
            'showing'     => 'Showing the :shown most recent of :total records.',
            'close'       => 'Close',

            'columns' => [
                'name'             => 'Name',
                'type'             => 'Type',
                'contact'          => 'Contact',
                'email'            => 'Email',
                'salesperson'      => 'Salesperson',
                'expected-revenue' => 'Expected Revenue',
                'stage'            => 'Stage',
            ],
        ],

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
    ],

    'team' => [
        'company-helper'         => 'Companies switched on in the top bar. To create a team for another company, switch to it first.',
        'company-not-active'     => 'This company is not active. Switch to it in the top bar first.',
        'section'                => 'Assignment Rules',
        'section-description'    => 'Which leads rule-based assignment gives this team.',
        'capacity'               => ':assigned leads assigned this month on a maximum of :max',
        'use-leads'              => 'Leads',
        'use-opportunities'      => 'Opportunities',
        'skip-assignment'        => 'Skip auto assignment',
        'skip-assignment-helper' => 'Leads are not assigned to this team automatically.',

        'assign-leads' => [
            'label'          => 'Assign Leads',
            'heading'        => 'Assign leads',
            'description'    => 'Pick the leads to share out. Each one goes to the team member with the most free space.',
            'submit'         => 'Assign',
            'records'        => 'Leads to assign',
            'empty'          => 'Select at least one lead.',
            'skipped'        => ':count could not be given out (no free space, or a member\'s rules do not match).',
            'capacity'       => 'Free space: :members',
            'no-members'     => 'No team member can take leads right now.',
            'nothing'        => 'No leads to assign right now.',
            'notification'   => 'Assigned :count lead(s) to salespeople.',
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
            'removed-from' => 'Removed from: :teams (Salespeople in Multiple Teams is off).',

            'single-team' => [
                'title' => 'Salespeople can only be in one team',
            ],
        ],
    ],
];
