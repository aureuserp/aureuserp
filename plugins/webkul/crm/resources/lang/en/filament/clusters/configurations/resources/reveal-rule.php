<?php

return [
    'model-label'        => 'Visitors to Leads Rule',
    'plural-model-label' => 'Visitors to Leads Rules',

    'navigation' => [
        'title' => 'Visitors to Leads Rules',
        'group' => 'Lead Generation',
    ],

    'form' => [
        'name'                     => 'Rule Name',
        'name-placeholder'         => 'e.g. US Visitors',
        'active'                   => 'Active',
        'countries'                => 'Countries',
        'countries-helper'         => 'Found from the visitor\'s IP address. Empty = all countries.',
        'states'                   => 'States',
        'states-helper'            => 'Known only for logged-in customers (from their address). Empty = all states.',
        'url'                      => 'URL Expression',
        'url-placeholder'          => 'e.g. /page',
        'url-helper'               => 'Pages that count, as a regular expression on the page path. Empty = whole website, "/" = homepage only, "/pricing|/shop" = pages containing /pricing or /shop.',
        'url-invalid'              => 'Enter a valid expression.',
        'type'                     => 'Type',
        'suffix'                   => 'Suffix',
        'suffix-helper'            => 'Added to the lead name, e.g. "Website visitor #12 (India) - Pricing".',
        'team'                     => 'Sales Team',
        'salesperson'              => 'Salesperson',
        'tags'                     => 'Tags',
        'priority'                 => 'Priority',
        'company'                  => 'Company',
        'company-helper'           => 'Empty = the customer\'s company or the first active company.',
        'lead-for'                 => 'Data Tracking',
        'industries'               => 'Industries',
        'industries-helper'        => 'Type a name to add a new industry.',
        'filter-on-size'           => 'Filter on Size',
        'size-min'                 => 'Company Size From',
        'size-max'                 => 'Company Size To',
        'employees'                => 'employees',
        'gdpr'                     => 'Make sure you know if you have to be GDPR compliant for storing personal data.',
        'contact-filter'           => 'Filter Contacts On',
        'extra-contacts'           => 'Number of Contacts',
        'extra-contacts-helper'    => '1 to 5 contacts per company.',
        'preferred-role'           => 'Preferred Role',
        'other-roles'              => 'Other Roles',
        'seniority'                => 'Seniority',

        'sections' => [
            'traffic'          => 'Website Traffic Conditions',
            'generation'       => 'Opportunity Generation Conditions',
            'lead-generation'  => 'Lead Generation Conditions',
            'contact-filter'   => 'Contact Filter',
            'lead-data'        => 'Lead Data',
            'opportunity-data' => 'Opportunity Data',
            'settings'         => 'Settings',
        ],
    ],

    'table' => [
        'name'          => 'Rule Name',
        'url'           => 'URL Expression',
        'whole-website' => 'Whole website',
        'countries'     => 'Countries',
        'all-countries' => 'All countries',
        'type'          => 'Type',
        'team'          => 'Sales Team',
        'salesperson'   => 'Salesperson',
        'leads'         => 'Leads',
        'opportunities' => 'Opportunities',
        'active'        => 'Active',
    ],

    'pages' => [
        'leads' => [
            'navigation' => 'Generated Leads',
            'title'      => 'Generated Leads: :rule',

            'columns' => [
                'name'       => 'Name',
                'country'    => 'Country',
                'stage'      => 'Stage',
                'created-at' => 'Created',
            ],
        ],
    ],
];
