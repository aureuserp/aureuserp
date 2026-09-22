<?php

return [
    'unknown'      => 'Inconnu',
    'other'        => 'Autres',
    'share'        => 'Part',
    'status'       => 'Statut',
    'stats'        => [
        'heading'              => 'Indicateurs Clés',
        'draft-rfqs'           => 'Demandes en Brouillon',
        'sent-rfqs'            => 'Demandes Envoyées',
        'confirmed-orders'     => 'Commandes Confirmées',
        'total-purchase-value' => 'Valeur Totale d\'Achat',
        'increase'             => ':percent% d’augmentation',
        'decrease'             => ':percent% de diminution',
        'no-change'            => 'Aucun changement',
    ],

    'trend'        => [
        'heading' => 'Évolution des Commandes par État',
        'states'  => [
            'draft'    => 'Brouillon',
            'sent'     => 'Envoyé',
            'purchase' => 'Achat',
            'done'     => 'Terminé',
            'canceled' => 'Annulé',
        ],
    ],

    'vendor-spend' => [
        'heading'         => 'Part des Dépenses par Fournisseur',
        'total-purchased' => 'Total Acheté',
    ],

    'top-orders'   => [
        'heading' => 'Principales Commandes',
        'columns' => [
            'reference'  => 'Numéro de Commande',
            'vendor'     => 'Fournisseur',
            'ordered-at' => 'Date de Commande',
            'amount'     => 'Montant Total',
        ],
    ],

    'top-products' => [
        'heading' => 'Produits les Plus Achetés',
        'columns' => [
            'name'     => 'Produit',
            'quantity' => 'Quantité Totale',
            'value'    => 'Valeur Totale',
        ],
    ],
];
