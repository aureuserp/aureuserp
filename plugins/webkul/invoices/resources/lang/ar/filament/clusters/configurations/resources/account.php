<?php

return [
    'title' => 'Accounts',

    'navigation' => [
        'title' => 'Accounts',
        'group' => 'Accounting',
    ],

    'global-search' => [
        'currency' => 'Currency',
        'name'     => 'Name',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'code'          => 'Code',
                'account-name'  => 'Account Name',
                'accounting'    => 'Accounting',
                'account-type'  => 'Account Type',
                'default-taxes' => 'Default Taxes',
                'tags'          => 'Tags',
                'journals'      => 'Journals',
                'currency'      => 'Currency',
                'deprecated'    => 'Deprecated',
                'reconcile'     => 'Reconcile',
                'non-trade'     => 'Non Trade',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'         => 'Code',
            'account-name' => 'Account Name',
            'account-type' => 'Account Type',
            'currency'     => 'Currency',
            'deprecated'   => 'Deprecated',
            'reconcile'    => 'Reconcile',
            'non-trade'    => 'Non Trade',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Account deleted',
                    'body'  => 'The account has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Accounts deleted',
                    'body'  => 'The accounts has been deleted successfully.',
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

    'infolist' => [
        'sections' => [
            'entries' => [
                'code'          => 'Code',
                'account-name'  => 'Account Name',
                'accounting'    => 'Accounting',
                'account-type'  => 'Account Type',
                'default-taxes' => 'Default Taxes',
                'tags'          => 'Tags',
                'journals'      => 'Journals',
                'currency'      => 'Currency',
                'deprecated'    => 'Deprecated',
                'reconcile'     => 'Reconcile',
                'non-trade'     => 'Non Trade',
            ],
        ],
    ],
];
