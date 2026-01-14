<?php

return [
    'post-action-validate' => [
        'customer-required'    => 'Please provide a valid Customer to proceed with the Customer Invoice validation.',
        'vendor-required'      => 'Please provide a valid Vendor to proceed with the Vendor Bill validation.',
        'bank-archived'        => 'The selected Partner Bank attached to this invoice is archived.',
        'negative-amount'      => 'Invoice can not be confirmed with a negative total amount.',
        'date-required'        => 'Please provide a valid Bill/Refund Date to proceed with the Bill/Refund validation.',
        'currency-archived'    => 'You cannot confirm an invoice with an archived currency.',
        'account-deprecated'   => 'One or more lines in this invoice are using deprecated accounts.',
        'lines-required'       => 'Please add at least one line to the invoice.',
        'draft-state-required' => 'Only invoices in draft state can be confirmed.',
        'journal-archived'     => 'You cannot confirm an invoice with an archived journal.',
    ],
];
