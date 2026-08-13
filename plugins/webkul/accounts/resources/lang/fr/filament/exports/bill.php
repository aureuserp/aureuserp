<?php

return [
    'columns' => [
        'number'          => 'Numéro',
        'state'           => 'État',
        'customer'        => 'Client',
        'bill-date'       => 'Date de facture',
        'due-date'        => 'Date d\'échéance',
        'tax-excluded'    => 'Hors taxe',
        'tax'             => 'Taxe',
        'total'           => 'Total',
        'amount-due'      => 'Montant dû',
        'payment-state'   => 'État du paiement',
        'checked'         => 'Vérifié',
        'accounting-date' => 'Date comptable',
        'source-document' => 'Document source',
        'reference'       => 'Référence',
        'sales-person'    => 'Vendeur',
        'bill-currency'   => 'Devise de la facture',
    ],

    'values' => [
        'yes' => 'Oui',
        'no'  => 'Non',
    ],

    'notification' => [
        'completed' => 'Votre export de factures fournisseur est terminé et :count ligne(s) exportée(s).',
        'failed'    => ':count ligne(s) n\'ont pas pu être exportée(s).',
    ],
];
