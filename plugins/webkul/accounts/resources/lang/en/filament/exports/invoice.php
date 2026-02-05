<?php

return [
    'columns' => [
        'number'           => 'Number',
        'state'            => 'State',
        'customer'         => 'Customer',
        'invoice-date'     => 'Invoice Date',
        'due-date'         => 'Due Date',
        'tax-excluded'     => 'Tax Excluded',
        'tax'              => 'Tax',
        'total'            => 'Total',
        'amount-due'       => 'Amount Due',
        'payment-state'    => 'Payment State',
        'checked'          => 'Checked',
        'accounting-date'  => 'Accounting Date',
        'source-document'  => 'Source Document',
        'reference'        => 'Reference',
        'sales-person'     => 'Sales Person',
        'invoice-currency' => 'Invoice Currency',
    ],

    'values' => [
        'yes' => 'Yes',
        'no'  => 'No',
    ],

    'notification' => [
        'completed' => 'Your invoice export has completed and :count row(s) exported.',
        'failed'    => ':count row(s) failed to export.',
    ],
];
