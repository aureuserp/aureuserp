<?php

return [
    'post-action-validate' => [
        'customer-required'    => 'Veuillez fournir un client valide pour procéder à la validation de la facture client.',
        'vendor-required'      => 'Veuillez fournir un fournisseur valide pour procéder à la validation de la facture fournisseur.',
        'bank-archived'        => 'La banque du partenaire associée à cette facture est archivée.',
        'negative-amount'      => 'La facture ne peut pas être confirmée avec un montant total négatif.',
        'date-required'        => 'Veuillez fournir une date de facture/avoir valide pour procéder à la validation de la facture/de l\'avoir.',
        'currency-archived'    => 'Vous ne pouvez pas confirmer une facture avec une devise archivée.',
        'account-deprecated'   => 'Une ou plusieurs lignes de cette facture utilisent des comptes obsolètes.',
        'lines-required'       => 'Veuillez ajouter au moins une ligne à la facture.',
        'draft-state-required' => 'Seules les factures à l\'état brouillon peuvent être confirmées.',
        'journal-archived'     => 'Vous ne pouvez pas confirmer une facture avec un journal archivé.',
    ],

    'documents' => [
        'titles' => [
            'invoice'     => 'Facture ID #:name',
            'bill'        => 'Facture fournisseur ID #:name',
            'refund'      => 'Avoir fournisseur ID #:name',
            'credit-note' => 'Avoir client ID #:name',
        ],

        'labels' => [
            'invoice-date'          => 'Date de facture',
            'bill-date'             => 'Date',
            'refund-date'           => 'Date de l\'avoir fournisseur',
            'credit-note-date'      => 'Date de l\'avoir client',
            'source'                => 'Source',
            'due-date'              => 'Date d\'échéance',
            'product'               => 'Produit',
            'quantity'              => 'Quantité',
            'unit'                  => 'Unité',
            'unit-price'            => 'Prix unitaire',
            'subtotal'              => 'Sous-total',
            'tax'                   => 'Taxe',
            'discount'              => 'Remise',
            'grand-total'           => 'Total général',
            'payment-information'   => 'Informations de paiement',
            'payment-communication' => 'Communication de paiement',
            'account-details'       => 'sur les coordonnées de ce compte :',
        ],
    ],
];
