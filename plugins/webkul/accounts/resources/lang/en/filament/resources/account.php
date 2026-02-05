<?php

return [
    'global-search' => [
        'code' => 'Code',
        'type' => 'Type',
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
                'reconcile'     => 'Allow Reconcile',
                'non-trade'     => 'Non Trade',
                'companies'     => 'Companies',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'         => 'Code',
            'account-name' => 'Account Name',
            'account-type' => 'Account',
            'currency'     => 'Currency',
            'journals'     => 'Journals',
            'reconcile'    => 'Allow Reconcile',
        ],

        'grouping' => [
            'account-type' => 'Account Type',
        ],

        'filters' => [
            'account-type'     => 'Account Type',
            'allow-reconcile'  => 'Allow Reconcile',
            'currency'         => 'Currency',
            'account-journals' => 'Journals',
            'non-trade'        => 'Non Trade',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Account updated',
                    'body'  => 'The account has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Account deleted',
                        'body'  => 'The account has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Account deletion failed',
                        'body'  => 'The account could not be deleted because it has associated journal items.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Accounts deleted',
                        'body'  => 'The accounts has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Accounts deletion failed',
                        'body'  => 'The accounts could not be deleted because they have associated journal items.',
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
