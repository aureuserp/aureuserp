<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'company'    => 'Company',
                    'avatar'     => 'Avatar',
                    'tax-id'     => 'Tax ID',
                    'job-title'  => 'Job Title',
                    'phone'      => 'Phone',
                    'mobile'     => 'Mobile',
                    'email'      => 'Email',
                    'website'    => 'Website',
                    'title'      => 'Title',
                    'name'       => 'Name',
                    'short-name' => 'Short Name',
                    'tags'       => 'Tags',
                    'color'      => 'Color',
                ],

                'address' => [
                    'title' => 'Address',

                    'fields' => [
                        'street1'  => 'Street 1',
                        'street2'  => 'Street 2',
                        'city'     => 'City',
                        'zip'      => 'ZIP',
                        'state'    => 'State',
                        'country'  => 'Country',
                        'name'     => 'Name',
                        'code'     => 'Code',
                    ],
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Sales and Purchases',

                'fields' => [
                    'responsible'           => 'Responsible',
                    'responsible-hint-text' => 'This is internal salesperson responsible for this customer',
                    'company-id'            => 'Company ID',
                    'company-id-hint-text'  => 'The company\'s registry number, used if different from the Tax ID. It must be unique among all partners within the same country.',
                    'reference'             => 'Reference',
                    'industry'              => 'Industry',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'parent'     => 'Parent',
        ],

        'groups' => [
            'account-type' => 'Account Type',
            'parent'       => 'Parent',
            'title'        => 'Title',
            'job-title'    => 'Job Title',
            'industry'     => 'Industry',
        ],

        'filters' => [
            'account-type'     => 'Account Type',
            'name'             => 'Name',
            'email'            => 'Email',
            'parent'           => 'Parent',
            'title'            => 'Title',
            'tax-id'           => 'Tax ID',
            'phone'            => 'Phone',
            'mobile'           => 'Mobile',
            'job-title'        => 'Job Title',
            'website'          => 'Website',
            'company-registry' => 'Company Registry',
            'responsible'      => 'Responsible',
            'reference'        => 'Reference',
            'parent'           => 'Parent',
            'creator'          => 'Creator',
            'company'          => 'Company',
            'industry'         => 'Industry',
            'industry'         => 'Industry',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Contact updated',
                    'body'  => 'The contact has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Contact restored',
                    'body'  => 'The contact has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Contact deleted',
                    'body'  => 'The contact has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contact force deleted',
                        'body'  => 'The contact has been force deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Contact could not be deleted',
                        'body'  => 'The contact cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Contacts restored',
                    'body'  => 'The contacts has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Contacts deleted',
                    'body'  => 'The contacts has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contacts force deleted',
                        'body'  => 'The contacts has been force deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Contacts could not be deleted',
                        'body'  => 'The contacts cannot be deleted because they are currently in use.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'company'    => 'Company',
                    'avatar'     => 'Avatar',
                    'tax-id'     => 'Tax ID',
                    'job-title'  => 'Job Title',
                    'phone'      => 'Phone',
                    'mobile'     => 'Mobile',
                    'email'      => 'Email',
                    'website'    => 'Website',
                    'title'      => 'Title',
                    'name'       => 'Name',
                    'short-name' => 'Short Name',
                    'tags'       => 'Tags',
                ],

                'address' => [
                    'title' => 'Address',

                    'fields' => [
                        'street1'  => 'Street 1',
                        'street2'  => 'Street 2',
                        'city'     => 'City',
                        'zip'      => 'ZIP',
                        'state'    => 'State',
                        'country'  => 'Country',
                        'name'     => 'Name',
                        'code'     => 'Code',
                    ],
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Sales and Purchases',

                'fields' => [
                    'responsible'           => 'Responsible',
                    'responsible-hint-text' => 'This is internal salesperson responsible for this customer',
                    'company-id'            => 'Company ID',
                    'company-id-hint-text'  => 'The registry number of the company. Use it if it is different from the Tax ID. It must be unique across all partners of a same country',
                    'reference'             => 'Reference',
                    'industry'              => 'Industry',
                ],
            ],
        ],
    ],
];
