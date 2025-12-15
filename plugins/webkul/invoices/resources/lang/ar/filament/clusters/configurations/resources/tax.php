<?php

return [
    'title' => 'Taxes',

    'navigation' => [
        'title' => 'Taxes',
        'group' => 'Accounting',
    ],

    'global-search' => [
        'company'     => 'Company',
        'amount-type' => 'Amount Type',
        'name'        => 'Name',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'            => 'Name',
                'tax-type'        => 'Tax Type',
                'tax-computation' => 'Tax Computation',
                'tax-scope'       => 'Tax Scope',
                'status'          => 'Status',
                'amount'          => 'Amount',
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Advanced Options',

                    'fields' => [
                        'invoice-label'       => 'Invoice label',
                        'tax-group'           => 'Tax Group',
                        'country'             => 'Country',
                        'include-in-price'    => 'Include in price',
                        'include-base-amount' => 'Include base amount',
                        'is-base-affected'    => 'Is base affected',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title'  => 'Description & Invoice Legal Notes',
                    'fields' => [
                        'description' => 'Description',
                        'legal-notes' => 'Legal Notes',
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                    => 'Name',
            'amount-type'             => 'Amount Type',
            'company'                 => 'Company',
            'tax-group'               => 'Tax Group',
            'country'                 => 'Country',
            'type-tax-use'            => 'Type Tax Use',
            'tax-scope'               => 'Tax Scope',
            'amount-type'             => 'Amount Type',
            'invoice-label'           => 'Invoice Label',
            'tax-exigibility'         => 'Tax Exigibility',
            'price-include-override'  => 'Price Include Override',
            'amount'                  => 'Amount',
            'status'                  => 'Status',
            'include-base-amount'     => 'Include Base Amount',
            'is-base-affected'        => 'Is Base Affected',
        ],

        'groups' => [
            'name'         => 'Name',
            'company'      => 'Company',
            'tax-group'    => 'Tax Group',
            'country'      => 'Country',
            'created-by'   => 'Created By',
            'type-tax-use' => 'Type Tax Use',
            'tax-scope'    => 'Tax Scope',
            'amount-type'  => 'Amount Type',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Payment Term deleted',
                    'body'  => 'The payment term has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Taxes deleted',
                    'body'  => 'The taxes has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'            => 'Name',
                'tax-type'        => 'Tax Type',
                'tax-computation' => 'Tax Computation',
                'tax-scope'       => 'Tax Scope',
                'status'          => 'Status',
                'amount'          => 'Amount',
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Advanced Options',

                    'entries' => [
                        'invoice-label'       => 'Invoice label',
                        'tax-group'           => 'Tax Group',
                        'country'             => 'Country',
                        'include-in-price'    => 'Include in price',
                        'include-base-amount' => 'Include base amount',
                        'is-base-affected'    => 'Is base affected',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title'   => 'Description & Invoice Legal Notes',
                    'entries' => [
                        'description' => 'Description',
                        'legal-notes' => 'Legal Notes',
                    ],
                ],
            ],
        ],
    ],

];
