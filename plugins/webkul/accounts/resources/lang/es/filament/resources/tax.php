<?php

return [
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
                        'include-in-price'    => 'Included in Price',
                        'include-base-amount' => 'Affect Base of Subsequent Taxes',
                        'is-base-affected'    => 'Base Affected by Previous Taxes',
                    ],
                ],

                'fields' => [
                    'description' => 'Description',
                    'legal-notes' => 'Legal Notes',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                   => 'Name',
            'amount-type'            => 'Amount Type',
            'company'                => 'Company',
            'tax-group'              => 'Tax Group',
            'country'                => 'Country',
            'tax-type'               => 'Tax Type',
            'tax-scope'              => 'Tax Scope',
            'amount-type'            => 'Amount Type',
            'invoice-label'          => 'Invoice Label',
            'tax-exigibility'        => 'Tax Exigibility',
            'price-include-override' => 'Price Include Override',
            'amount'                 => 'Amount',
            'status'                 => 'Status',
            'include-base-amount'    => 'Include Base Amount',
            'is-base-affected'       => 'Is Base Affected',
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
                    'success' => [
                        'title' => 'Tax deleted',
                        'body'  => 'The Tax has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Tax could not be deleted',
                        'body'  => 'The tax cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Taxes deleted',
                        'body'  => 'The taxes has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Taxes could not be deleted',
                        'body'  => 'The taxes cannot be deleted because they are currently in use.',
                    ],
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
