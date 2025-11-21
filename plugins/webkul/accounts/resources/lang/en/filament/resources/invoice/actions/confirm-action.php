<?php

return [
    'title' => 'Confirm',

    'validation' => [
        'notification' => [
            'error' => [
                'customer-required' => [
                    'title' => 'Customer is required',
                    'body'  => 'Please provide a valid Customer to proceed with the Customer Invoice validation.',
                ],

                'vendor-required' => [
                    'title' => 'Vendor is required',
                    'body'  => 'Please provide a valid Vendor to proceed with the Vendor Bill validation.',
                ],

                'bank-archived' => [
                    'title' => 'Partner Bank is archived',
                    'body'  => 'The selected Partner Bank attached to this invoice is archived.',
                ],

                'negative-amount' => [
                    'title' => 'Negative Total Amount',
                    'body'  => 'Invoice can not be confirmed with a negative total amount.',
                ],

                'date-required' => [
                    'title' => 'Bill/Refund Date is required',
                    'body'  => 'Please provide a valid Bill/Refund Date to proceed with the Bill/Refund validation.',
                ],

                'currency-archived' => [
                    'title' => 'Currency is archived',
                    'body'  => 'You cannot confirm an invoice with an archived currency.',
                ],

                'account-deprecated' => [
                    'title' => 'Deprecated Account in Lines',
                    'body'  => 'One or more lines in this invoice are using deprecated accounts.',
                ],

                'lines-required' => [
                    'title' => 'Move Line validation',
                    'body'  => 'Please add at least one line to the invoice.',
                ],

                'draft-state-required' => [
                    'title' => 'Invalid Move State',
                    'body'  => 'Only invoices in draft state can be confirmed.',
                ],

                'journal-archived' => [
                    'title' => 'Journal is archived',
                    'body'  => 'You cannot confirm an invoice with an archived journal.',
                ],
            ],
        ],
    ],
];
