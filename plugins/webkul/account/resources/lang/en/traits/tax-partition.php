<?php

return [
    'form' => [
        'factor-percent'    => 'Factor Percent',
        'factor-ratio'      => 'Factor Ratio',
        'repartition-type'  => 'Repartition Type',
        'document-type'     => 'Document Type',
        'account'           => 'Account',
        'tax'               => 'Tax',
        'tax-closing-entry' => 'Tax Closing Entry',
    ],

    'table' => [
        'columns' => [
            'factor-percent'    => 'Factor Percent(%)',
            'account'           => 'Account',
            'tax'               => 'Tax',
            'company'           => 'Company',
            'repartition-type'  => 'Repartition Type',
            'document-type'     => 'Document Type',
            'tax-closing-entry' => 'Tax Closing Entry',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tax Partition updated',
                    'body'  => 'The tax partition has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tax Partition Term deleted',
                    'body'  => 'The tax Partition term has been deleted successfully.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Tax Partition Term created',
                    'body'  => 'The tax Partition term has been created successfully.',
                ],
            ],
        ],
    ],
];
