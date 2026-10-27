<?php

return [
    'model-label'        => 'Contact',
    'plural-model-label' => 'Contacts',

    'navigation' => [
        'title' => 'Contacts',
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
        'unauthorized' => 'This contact cannot be moved to that column.',
    ],

    'actions' => [
        'merge' => [
            'label'              => 'Merge',
            'heading'            => 'Merge contacts',
            'description'        => 'Everything linked to the other contacts (opportunities, orders, child contacts, messages…) moves to the contact you keep. Empty fields on it are filled from the others, and the others are archived.',
            'submit'             => 'Merge Contacts',
            'destination'        => 'Contact to keep',
            'destination-helper' => 'The other selected contacts are merged into this one.',
            'invalid'            => 'Select at least two contacts you are allowed to edit and archive.',
            'notification'       => ':count contact(s) merged into :name.',
            'log'                => 'Merged contacts: :names',
        ],

        'vcard' => [
            'label' => 'Download (vCard)',
        ],

        'portal' => [
            'label'        => 'Grant portal access',
            'description'  => 'Each selected contact with an email address receives an invitation to set a portal password.',
            'notification' => 'Portal access granted to :granted contact(s), :skipped skipped (no email, already has access, email in use or no access).',
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
