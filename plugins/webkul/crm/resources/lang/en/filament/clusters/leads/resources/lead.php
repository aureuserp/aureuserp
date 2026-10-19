<?php

return [
    'model-label'        => 'Lead',
    'plural-model-label' => 'Leads',

    'navigation' => [
        'title' => 'Leads',
    ],

    'views' => [
        'kanban' => 'Kanban',
        'list'   => 'List',
    ],

    'global-search' => [
        'contact' => 'Contact',
        'company' => 'Company',
    ],

    'form' => [
        'tabs' => [
            'notes'      => 'Notes',
            'extra-info' => 'Extra Info',
        ],

        'fieldsets' => [
            'marketing' => 'Marketing',
            'analysis'  => 'Analysis',
        ],

        'fields' => [
            'name-placeholder'    => 'e.g. Product Pricing',
            'probability'         => 'Probability',
            'company-name'        => 'Company Name',
            'address'             => 'Address',
            'street1'             => 'Street...',
            'street2'             => 'Street 2...',
            'city'                => 'City',
            'zip'                 => 'ZIP',
            'state'               => 'State',
            'country'             => 'Country',
            'website'             => 'Website',
            'website-placeholder' => 'e.g. https://www.example.com',
            'salesperson'         => 'Salesperson',
            'team'                => 'Sales Team',
            'contact-name'        => 'Contact Name',
            'email'               => 'Email',
            'job-position'        => 'Job Position',
            'phone'               => 'Phone',
            'priority'            => 'Priority',
            'tags'                => 'Tags',
            'company'             => 'Company',
            'campaign'            => 'Campaign',
            'medium'              => 'Medium',
            'source'              => 'Source',
            'referred-by'         => 'Referred By',
            'assignment-date'     => 'Assignment Date',
            'closed-date'         => 'Closed Date',
        ],
    ],

    'infolist' => [
        'sections' => [
            'lost' => 'Lost',
        ],

        'entries' => [
            'lost-reason'  => 'Lost Reason',
            'closing-note' => 'Closing Note',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Lead',
            'contact-name' => 'Contact Name',
            'company-name' => 'Company Name',
            'email'        => 'Email',
            'phone'        => 'Phone',
            'city'         => 'City',
            'country'      => 'Country',
            'salesperson'  => 'Salesperson',
            'team'         => 'Sales Team',
            'priority'     => 'Priority',
            'tags'         => 'Tags',
            'source'       => 'Source',
            'lost-reason'  => 'Lost Reason',
            'created-at'   => 'Created On',
        ],
    ],

    'actions' => [
        'create' => [
            'label' => 'New Lead',
        ],

        'open' => [
            'label' => 'Open',
        ],

        'schedule-activity' => [
            'label' => 'Schedule Activity',
        ],

        'convert' => [
            'label' => 'Convert to Opportunity',

            'modal' => [
                'heading' => 'Convert to opportunity',
                'submit'  => 'Create Opportunity',
            ],

            'form' => [
                'customer'          => 'Customer',
                'existing-customer' => 'Existing Customer',
                'salesperson'       => 'Salesperson',
                'team'              => 'Sales Team',

                'customer-options' => [
                    'create' => 'Create a new customer',
                    'link'   => 'Link to an existing customer',
                    'none'   => 'Do not link to a customer',
                ],
            ],

            'notification' => [
                'title' => 'Converted to opportunity',
                'body'  => '":name" is now an opportunity in the pipeline.',

                'invalid' => [
                    'title' => 'Invalid selection',
                    'body'  => 'The selected customer, salesperson or sales team does not belong to this lead\'s company.',
                ],
            ],
        ],

        'lost' => [
            'notification' => [
                'title' => 'Lead lost',
                'body'  => 'The lead has been marked as lost and moved to the Lost view.',
            ],
        ],

        'restore' => [
            'notification' => [
                'title' => 'Lead restored',
                'body'  => 'The lead has been restored.',
            ],
        ],

        'force-delete' => [
            'notification' => [
                'title' => 'Lead deleted',
                'body'  => 'The lead has been permanently deleted.',
            ],
        ],
    ],
];
