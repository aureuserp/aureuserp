<?php

return [
    'model-label'        => 'Recurring Plan',
    'plural-model-label' => 'Recurring Plans',

    'navigation' => [
        'title' => 'Recurring Plans',
    ],

    'header-actions' => [
        'create' => 'New Recurring Plan',
    ],

    'form' => [
        'name'             => 'Plan name',
        'name-placeholder' => 'e.g. Monthly',
        'months'           => 'Number of months',
        'months-helper'    => 'How many months the recurring revenue covers (Monthly = 1, Yearly = 12).',
    ],

    'table' => [
        'columns' => [
            'name'          => 'Plan',
            'months'        => 'Months',
            'opportunities' => 'Opportunities',
        ],
    ],

    'tabs' => [
        'active'   => 'Active',
        'archived' => 'Archived',
    ],

    'notifications' => [
        'created'  => 'Recurring plan created',
        'updated'  => 'Recurring plan updated',
        'restored' => 'Recurring plan restored',
        'deleted'  => 'Recurring plan deleted',
    ],

    'opportunity' => [
        'recurring-revenue' => 'Recurring Revenue',
        'plan'              => 'Recurring Plan',
        'mrr'               => 'Monthly Recurring Revenue',
    ],
];
