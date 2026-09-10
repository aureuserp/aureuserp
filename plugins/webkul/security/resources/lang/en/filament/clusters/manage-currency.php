<?php

return [
    'breadcrumb' => 'Manage Currency',
    'title'      => 'Manage Currency',
    'group'      => 'General',

    'navigation' => [
        'label' => 'Manage Currency',
    ],

    'form' => [
        'default-currency' => [
            'label'       => 'Base Currency',
            'helper-text' => 'The base currency of the system. Product prices are stored in this currency and are converted to the company currency when a sale or purchase is made.',
        ],
    ],
];
