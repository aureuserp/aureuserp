<?php

return [
    'actions' => [
        'export' => [
            'label' => 'Export',
        ],

        'import' => [
            'label' => 'Import',
        ],
    ],

    'columns' => [
        'id'                 => 'ID',
        'name'               => 'Name',
        'contact-name'       => 'Contact Name',
        'company-name'       => 'Company Name',
        'job-title'          => 'Job Position',
        'email'              => 'Email',
        'phone'              => 'Phone',
        'website'            => 'Website',
        'street1'            => 'Street',
        'street2'            => 'Street 2',
        'city'               => 'City',
        'zip'                => 'ZIP',
        'country'            => 'Country',
        'stage'              => 'Stage',
        'salesperson'        => 'Salesperson',
        'team'               => 'Sales Team',
        'priority'           => 'Priority',
        'probability'        => 'Probability',
        'tags'               => 'Tags',
        'source'             => 'Source',
        'medium'             => 'Medium',
        'campaign'           => 'Campaign',
        'notes'              => 'Notes',
        'lost-reason'        => 'Lost Reason',
        'created-at'         => 'Created On',
        'customer'           => 'Customer',
        'expected-revenue'   => 'Expected Revenue',
        'prorated-revenue'   => 'Prorated Revenue',
        'expected-closing'   => 'Expected Closing',
        'number'             => 'Number',
        'order-date'         => 'Order Date',
        'expiration'         => 'Expiration',
        'delivery-date'      => 'Delivery Date',
        'customer-reference' => 'Customer Reference',
        'source-document'    => 'Source Document',
        'untaxed-amount'     => 'Untaxed Amount',
        'taxes'              => 'Taxes',
        'total'              => 'Total',
        'currency'           => 'Currency',
        'status'             => 'Status',
        'invoice-status'     => 'Invoice Status',
    ],

    'notifications' => [
        'export' => [
            'completed' => 'Your export is ready: :count rows exported.',
            'failed'    => ':count rows failed to export.',
        ],

        'import' => [
            'completed' => 'Your import has finished: :count rows imported.',
            'failed'    => ':count rows failed; download them from this notification to see why.',
        ],
    ],

    'errors' => [
        'record-not-found' => 'No record with ID :id was found in your companies.',
        'cannot-update'    => 'You are not allowed to update record :id.',
        'cannot-create'    => 'You are not allowed to create records.',
        'not-found'        => ':field ":value" was not found.',
    ],
];
