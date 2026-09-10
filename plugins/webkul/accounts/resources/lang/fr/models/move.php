<?php

return [
    'title'          => 'Pièce comptable',

    'titles'         => [
        'invoice'     => 'Facture',
        'credit-note' => 'Avoir',
        'bill'        => 'Facture fournisseur',
        'refund'      => 'Avoir fournisseur',
    ],

    'log-attributes' => [
        'name'                  => 'Référence de la facture',
        'reference'             => 'Référence',
        'date'                  => 'Date de facture',
        'state'                 => 'Statut de la facture',
        'move-type'             => 'Type de pièce',
        'checked'               => 'Vérifié',
        'payment-reference'     => 'Référence de paiement',
        'payment-state'         => 'Statut de paiement',
        'amount-untaxed'        => 'Sous-total',
        'invoice-source-email'  => 'E-mail source',
        'is-move-sent'          => 'Pièce envoyée',
        'invoice-origin'        => 'Origine de la facture',
        'currency'              => 'Devise',
        'partner'               => 'Partenaire',
        'partner-bank'          => 'Banque du partenaire',
        'invoice-user'          => 'Utilisateur de la facture',
        'fiscal-position'       => 'Position fiscale',
        'invoice-payment-term'  => 'Condition de paiement de la facture',
        'invoice-cash-rounding' => 'Arrondi de caisse de la facture',
    ],
];
