<?php

return [
    'form' => [
        'fieldsets' => [
            'account-properties' => [
                'label' => 'Account Properties',

                'fields' => [
                    'income-account'                    => 'Income Account',
                    'income-account-hint-tooltip'       => 'This account will be used when validating a customer invoice.',
                    'expense-account'                   => 'Expense Account',
                    'expense-account-hint-tooltip'      => 'The expense is recorded when a vendor bill is validated, except under Anglo-Saxon accounting with perpetual inventory valuation, where it is instead recognized as the Cost of Goods Sold when the customer invoice is validated.',
                    'down-payment-account'              => 'Down Payment Account',
                    'down-payment-account-hint-tooltip' => 'Select the account to which down payments from this category will be recorded.',
                ],
            ],
        ],
    ],
];
