<?php

return [
    'form' => [
        'tabs' => [
            'general-information' => [
                'title' => 'General Information',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Branch Information',

                        'fields' => [
                            'company-name'                => 'Company Name',
                            'registration-number'         => 'Registration Number',
                            'tax-id'                      => 'Tax ID',
                            'tax-id-tooltip'              => 'The Tax ID is a unique identifier for your company.',
                            'color'                       => 'Color',
                            'company-id'                  => 'Company ID',
                            'company-id-tooltip'          => 'The Company ID is a unique identifier for your company.',
                        ],
                    ],

                    'branding' => [
                        'title'  => 'Branding',
                        'fields' => [
                            'branch-logo' => 'Branch Logo',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Address Information',

                'sections' => [
                    'address-information' => [
                        'title' => 'Address Information',

                        'fields' => [
                            'street1'                => 'Street 1',
                            'street2'                => 'Street 2',
                            'city'                   => 'City',
                            'zip'                    => 'Zip Code',
                            'country'                => 'Country',
                            'country-currency-name'  => 'Currency Name',
                            'country-phone-code'     => 'Phone Code',
                            'country-code'           => 'Code',
                            'country-name'           => 'Country Name',
                            'country-state-required' => 'State Required',
                            'country-zip-required'   => 'Zip Required',
                            'country-create'         => 'Create Country',
                            'state'                  => 'State',
                            'state-name'             => 'State Name',
                            'state-code'             => 'State Code',
                            'zip-code'               => 'Zip Code',
                            'state-create'           => 'Create State',
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
                            'currency-create'         => 'Create Currency',
                            'company-foundation-date' => 'Company Foundation Date',
                            'status'                  => 'Status',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Contact Information',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Contact Information',

                        'fields' => [
                            'email-address' => 'Email Address',
                            'phone-number'  => 'Phone Number',
                            'mobile-number' => 'Phone Number',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Logo',
            'company-name'         => 'Branch Name',
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
            'company-name' => 'Branch Name',
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
            'trashed' => 'Trashed',
            'status'  => 'Status',
            'country' => 'Country',
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Branch created',
                    'body'  => 'The branch has been created successfully.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Branch updated',
                    'body'  => 'The branch has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Branch deleted',
                    'body'  => 'The branch has been deleted successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Branch restored',
                    'body'  => 'The branch has been restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Branches restored',
                    'body'  => 'The branches has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Branches deleted',
                    'body'  => 'The branches has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Branches force deleted',
                    'body'  => 'The branches has been force deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'general-information' => [
                'title' => 'General Information',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Branch Information',

                        'entries' => [
                            'company-name'                => 'Company Name',
                            'registration-number'         => 'Registration Number',
                            'registration-number-tooltip' => 'The Tax ID is a unique identifier for your company.',
                            'color'                       => 'Color',
                        ],
                    ],

                    'branding' => [
                        'title'   => 'Branding',
                        'entries' => [
                            'branch-logo' => 'Branch Logo',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Address Information',

                'sections' => [
                    'address-information' => [
                        'title' => 'Address Information',

                        'entries' => [
                            'street1'                => 'Street 1',
                            'street2'                => 'Street 2',
                            'city'                   => 'City',
                            'zip'                    => 'Zip Code',
                            'country'                => 'Country',
                            'country-currency-name'  => 'Currency Name',
                            'country-phone-code'     => 'Phone Code',
                            'country-code'           => 'Code',
                            'country-name'           => 'Country Name',
                            'country-state-required' => 'State Required',
                            'country-zip-required'   => 'Zip Required',
                            'country-create'         => 'Create Country',
                            'state'                  => 'State',
                            'state-name'             => 'State Name',
                            'state-code'             => 'State Code',
                            'zip-code'               => 'Zip Code',
                            'state-create'           => 'Create State',
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
                            'currency-create'         => 'Create Currency',
                            'company-foundation-date' => 'Company Foundation Date',
                            'status'                  => 'Status',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Contact Information',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Contact Information',

                        'entries' => [
                            'email-address' => 'Email Address',
                            'phone-number'  => 'Phone Number',
                            'mobile-number' => 'Phone Number',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
