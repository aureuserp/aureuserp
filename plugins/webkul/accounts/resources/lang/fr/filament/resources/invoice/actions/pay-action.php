<?php

return [
    'title' => 'Payer',

    'form' => [
        'fields' => [
            'journal'                => 'Journal',
            'amount'                 => 'Montant',
            'currency'               => 'Devise',
            'payment-method-line'    => 'Ligne de mode de paiement',
            'payment-date'           => 'Date de paiement',
            'recipient-bank-account' => 'Compte bancaire du bénéficiaire',
            'communication'          => 'Mémo',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title' => 'Échec du paiement',
        ],
    ],
];
