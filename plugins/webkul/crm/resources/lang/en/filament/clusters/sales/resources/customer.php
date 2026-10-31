<?php

return [
    'model-label'        => 'Customer',
    'plural-model-label' => 'Customers',

    'navigation' => [
        'title' => 'Customers',
    ],

    'table' => [
        'columns' => [
            'name'        => 'Name',
            'phone'       => 'Phone',
            'email'       => 'Email',
            'salesperson' => 'Salesperson',
            'city'        => 'City',
            'country'     => 'Country',
            'company'     => 'Company',
        ],
    ],

    'grid' => [
        'opportunities' => '{1} :count opportunity|[0,*] :count opportunities',
        'sale-orders'   => '{1} :count sales order|[0,*] :count sales orders',
    ],

    'board' => [
        'unauthorized' => 'This customer cannot be moved to that column.',
    ],

    'actions' => [
        'merge' => [
            'label'              => 'Merge',
            'heading'            => 'Merge customers',
            'description'        => 'Everything linked to the other customers (opportunities, orders, child contacts, messages…) moves to the customer you keep. Empty fields on it are filled from the others, and the others are archived.',
            'submit'             => 'Merge Customers',
            'destination'        => 'Customer to keep',
            'destination-helper' => 'The other selected customers are merged into this one.',
            'invalid'            => 'Select at least two customers you are allowed to edit and archive.',
            'notification'       => ':count customer(s) merged into :name.',
            'log'                => 'Merged customers: :names',
        ],

        'vcard' => [
            'label' => 'Download (vCard)',
        ],

        'portal' => [
            'label'        => 'Grant portal access',
            'description'  => 'Each selected customer with an email address receives an invitation to set a portal password.',
            'notification' => 'Portal access granted to :granted customer(s), :skipped skipped (no email, already has access, email in use or no access).',
        ],
    ],

    'infolist' => [
        'sections' => [
            'overview' => 'Overview',
        ],

        'fields' => [
            'opportunities' => 'Opportunities',
            'sale-orders'   => 'Sales Orders',
        ],
    ],
];
