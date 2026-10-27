<?php

return [
    'model-label'        => 'Sales Team',
    'plural-model-label' => 'Sales Teams',

    'navigation' => [
        'title' => 'Teams',
    ],

    'table' => [
        'columns' => [
            'name'               => 'Sales Team',
            'open-opportunities' => 'Open Opportunities',
            'expected-revenue'   => 'Expected Revenue',
            'members'            => 'Members',
            'company'            => 'Company',
        ],

        'filters' => [
            'team-leader' => 'Team Leader',
            'created-at'  => 'Created At',
        ],

        'actions' => [
            'pipeline' => [
                'label' => 'Pipeline',
            ],
        ],
    ],

    'grid' => [
        'new-opportunities'  => '{1} :count new opportunity|[0,*] :count new opportunities',
        'quotations'         => '{1} :count Quotation|[0,*] :count Quotations',
        'configuration'      => 'Configuration',
        'menu'               => 'Team menu',
        'open-opportunities' => '{1} :count Open Opportunity|[0,*] :count Open Opportunities',
        'orders-to-invoice'  => '{1} :count Order to Invoice|[0,*] :count Orders to Invoice',
        'chart'              => 'New opportunities per week',
        'target-progress'    => 'Invoiced this month: :invoiced of :target',
    ],

    'grid-menu' => [
        'view'          => 'View',
        'reporting'     => 'Reporting',
        'leads'         => 'Leads',
        'opportunities' => 'Opportunities',
        'quotations'    => 'Quotations',
        'sales-orders'  => 'Sales Orders',
        'forecast'      => 'Forecast',
        'activities'    => 'Activities',
        'no-color'      => 'No color',
    ],
];
