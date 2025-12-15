<?php

return [
    'title' => 'Companies',

    'navigation' => [
        'title' => 'Companies',
        'group' => 'Settings',
    ],

    'global-search' => [
        'name'  => 'Name',
        'email' => 'Email',
    ],

    'form' => [
        'sections' => [
            'company-information' => [
                'title'  => 'Company Information',
                'fields' => [
                    'name'                  => 'Company Name',
                    'registration-number'   => 'Registration Number',
                    'company-id'            => 'Company ID',
                    'tax-id'                => 'Tax ID',
                    'tax-id-tooltip'        => 'The Tax ID is a unique identifier for your company.',
                    'website'               => 'Website',
                ],
            ],

            'address-information' => [
                'title'  => 'Address Information',

                'fields' => [
                    'street1'        => 'Street 1',
                    'street2'        => 'Street 2',
                    'city'           => 'City',
                    'zipcode'        => 'Zip Code',
                    'country'        => 'Country',
                    'currency-name'  => 'Currency Name',
                    'phone-code'     => 'Phone Code',
                    'code'           => 'Code',
                    'country-name'   => 'Country Name',
                    'state-required' => 'State Required',
                    'zip-required'   => 'Zip Required',
                    'create-country' => 'Create Country',
                    'state'          => 'State',
                    'state-name'     => 'State Name',
                    'state-code'     => 'State Code',
                    'create-state'   => 'Create State',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Information',

                'fields' => [
                    'default-currency'        => 'Default Currency',
                    'currency-name'           => 'Currency Name',
                    'currency-full-name'      => 'Currency Full Name',
                    'currency-symbol'         => 'Currency Symbol',
                    'currency-iso-numeric'    => 'Currency ISO Numeric',
                    'currency-decimal-places' => 'Currency Decimal Places',
                    'currency-rounding'       => 'Currency Rounding',
                    'currency-status'         => 'Currency Status',
                    'company-foundation-date' => 'Company Foundation Date',
                    'currency-create'         => 'Create Currency',
                    'status'                  => 'Status',
                ],
            ],

            'branding' => [
                'title'  => 'Branding',
                'fields' => [
                    'company-logo' => 'Company Logo',
                    'color'        => 'Color',
                ],
            ],

            'contact-information' => [
                'title'  => 'Contact Information',
                'fields' => [
                    'email'  => 'Email Address',
                    'phone'  => 'Phone Number',
                    'mobile' => 'Phone Number',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Logo',
            'company-name'         => 'Company Name',
            'branches'             => 'Branches',
            'email'                => 'Email',
            'city'                 => 'City',
            'country'              => 'Country',
            'currency'             => 'Currency',
            'status'               => 'Status',
            'created-at'           => 'Created At',
            'updated-at'           => 'Updated At',
        ],

        'groups' => [
            'company-name' => 'Company Name',
            'city'         => 'City',
            'country'      => 'Country',
            'state'        => 'State',
            'email'        => 'Email',
            'phone'        => 'Phone',
            'currency'     => 'Currency',
            'created-at'   => 'Created At',
            'updated-at'   => 'Updated At',
        ],

        'filters' => [
            'status'  => 'Status',
            'country' => 'Country',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Company edited',
                    'body'  => 'The company has been edited successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Company deleted',
                    'body'  => 'The company has been deleted successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Company restored',
                    'body'  => 'The company has been restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Companies restored',
                    'body'  => 'The companies has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Companies deleted',
                    'body'  => 'The companies has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Companies force deleted',
                    'body'  => 'The companies has been force deleted successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Companies created',
                    'body'  => 'The companies has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'company-information' => [
                'title'   => 'Company Information',
                'entries' => [
                    'name'                  => 'Company Name',
                    'registration-number'   => 'Registration Number',
                    'company-id'            => 'Company ID',
                    'tax-id'                => 'Tax ID',
                    'tax-id-tooltip'        => 'The Tax ID is a unique identifier for your company.',
                    'website'               => 'Website',
                ],
            ],

            'address-information' => [
                'title'  => 'Address Information',

                'entries' => [
                    'street1'        => 'Street 1',
                    'street2'        => 'Street 2',
                    'city'           => 'City',
                    'zipcode'        => 'Zip Code',
                    'country'        => 'Country',
                    'currency-name'  => 'Currency Name',
                    'phone-code'     => 'Phone Code',
                    'code'           => 'Code',
                    'country-name'   => 'Country Name',
                    'state-required' => 'State Required',
                    'zip-required'   => 'Zip Required',
                    'create-country' => 'Create Country',
                    'state'          => 'State',
                    'state-name'     => 'State Name',
                    'state-code'     => 'State Code',
                    'create-state'   => 'Create State',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Information',

                'entries' => [
                    'default-currency'        => 'Default Currency',
                    'currency-name'           => 'Currency Name',
                    'currency-full-name'      => 'Currency Full Name',
                    'currency-symbol'         => 'Currency Symbol',
                    'currency-iso-numeric'    => 'Currency ISO Numeric',
                    'currency-decimal-places' => 'Currency Decimal Places',
                    'currency-rounding'       => 'Currency Rounding',
                    'currency-status'         => 'Currency Status',
                    'company-foundation-date' => 'Company Foundation Date',
                    'currency-create'         => 'Create Currency',
                    'status'                  => 'Status',
                ],
            ],

            'branding' => [
                'title'   => 'Branding',
                'entries' => [
                    'company-logo' => 'Company Logo',
                    'color'        => 'Color',
                ],
            ],

            'contact-information' => [
                'title'   => 'Contact Information',
                'entries' => [
                    'email'  => 'Email Address',
                    'phone'  => 'Phone Number',
                    'mobile' => 'Phone Number',
                ],
            ],
        ],
    ],
];
