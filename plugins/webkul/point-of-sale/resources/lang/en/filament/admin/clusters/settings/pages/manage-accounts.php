<?php

return [
    'title' => 'Accounting',

    'form' => [
        'fields' => [
            'receivable-account'      => 'Default Receivable Account',
            'stock-output-account'    => 'Stock Output Account',
            'balancing-account'       => 'Default Balancing Account',
            'enable-cogs'             => 'Post Cost of Goods Sold',
            'enable-cogs-helper-text' => 'Posts a cost entry from the product cost snapshot. There is no valuation layer yet, so the stock output account behaves as a cost-of-sales contra account.',
            'allow-balancing-line'    => 'Allow Balancing Line',
        ],
    ],
];
