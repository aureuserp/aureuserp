<?php

return [
    'title' => 'Manage Quotation & Order',

    'breadcrumb' => 'Manage Quotation & Order',

    'navigation' => [
        'title' => 'Manage Quotation & Order',
    ],

    'form' => [
        'fields' => [
            'validity-suffix'         => 'days',
            'validity'                => 'Default Quotation Validity',
            'validity-help'           => 'The default number of days a quotation is valid for.',
            'lock-confirm-sales'      => 'Lock Confirm Sales',
            'lock-confirm-sales-help' => 'If enabled, the sales order will be locked after confirmation.',
        ],
    ],
];
