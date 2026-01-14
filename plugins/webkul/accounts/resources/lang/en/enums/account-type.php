<?php

return [
    'assets' => [
        'label'   => 'Assets',
        'options' => [
            'receivable'  => 'Receivable',
            'cash'        => 'Bank and Cash',
            'current'     => 'Current Assets',
            'non-current' => 'Non-current Assets',
            'prepayments' => 'Prepayments',
            'fixed'       => 'Fixed Assets',
        ],
    ],

    'liabilities' => [
        'label'   => 'Liabilities',
        'options' => [
            'payable'     => 'Payable',
            'credit-card' => 'Credit Card',
            'current'     => 'Current Liabilities',
            'non-current' => 'Non-current Liabilities',
        ],
    ],

    'equity' => [
        'label'   => 'Equity',
        'options' => [
            'equity'     => 'Equity',
            'unaffected' => 'Current Year Earnings',
        ],
    ],

    'income' => [
        'label'   => 'Income',
        'options' => [
            'income' => 'Income',
            'other'  => 'Other Income',
        ],
    ],

    'expenses' => [
        'label'   => 'Expenses',
        'options' => [
            'expense'      => 'Expenses',
            'depreciation' => 'Depreciation',
            'direct-cost'  => 'Cost of Revenue',
        ],
    ],

    'off-balance' => [
        'label'   => 'Off-Balance Sheet',
        'options' => [
            'off-balance' => 'Off-Balance Sheet',
        ],
    ],
];
