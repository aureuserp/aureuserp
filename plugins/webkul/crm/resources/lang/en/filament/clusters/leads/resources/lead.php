<?php

return [
    'model-label'        => 'Lead',
    'plural-model-label' => 'Leads',

    'navigation' => [
        'title' => 'Leads',
    ],

    'global-search' => [
        'contact' => 'Contact',
        'company' => 'Company',
    ],

    'form' => [
        'sections' => [
            'general' => 'Lead',
            'sales'   => 'Sales',
        ],

        'tabs' => [
            'notes'      => 'Notes',
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
                'action'     => 'Conversion action',
                'duplicates' => 'Similar leads and opportunities to merge',

                'customer'          => 'Customer',
                'existing-customer' => 'Existing Customer',
                'salesperson'       => 'Salesperson',
                'team'              => 'Sales Team',
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

        'mass-convert' => [
            'label' => 'Convert to Opportunities',

            'modal' => [
                'heading' => 'Convert to opportunities',
                'submit'  => 'Convert',
            ],

            'form' => [
                'deduplicate'             => 'Apply deduplication',
                'deduplicate-helper'      => 'Merge each lead with its similar leads and opportunities (same email, phone or customer).',
                'salespeople'             => 'Salespeople',
                'salespeople-helper'      => 'Leads without a salesperson are shared between them in turn.',
                'force-assignment'        => 'Force assignment',
                'force-assignment-helper' => 'Also reassign leads that already have a salesperson.',

                'customer-options' => [
                    'create' => 'Link to an existing customer or create one',
                    'link'   => 'Link to an existing customer',
                ],
            ],

            'notification' => [
                'title' => 'Leads converted',
                'body'  => ':count lead(s) converted, :merged similar record(s) merged, :skipped skipped.',
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
