<?php

return [
    'title' => 'Commandes',

    'navigation' => [
        'label' => 'Commandes',
    ],

    'walk-in' => 'Client de passage',

    'search' => 'Rechercher par commande, reçu ou client',

    'search-restaurant' => 'Rechercher par commande, reçu, client ou table',

    'select-order' => 'Sélectionnez une commande pour voir ses lignes.',

    'line-discount' => '−:discount% de remise',

    'taxes' => 'Taxes',

    'total' => 'Total',

    'status' => [
        'active' => 'Toutes les commandes actives',
    ],

    'columns' => [
        'date'     => 'Date',
        'receipt'  => 'Numéro de reçu',
        'order'    => 'Numéro de commande',
        'customer' => 'Client',
        'cashier'  => 'Caissier',
        'tracking' => 'Numéro de suivi',
        'table'    => 'Table',
        'total'    => 'Total',
        'status'   => 'Statut',
    ],

    'refund' => [
        'prompt'    => 'Sélectionnez le(s) produit(s) à rembourser et indiquez la quantité',
        'refunded'  => 'Remboursé :',
        'to-refund' => 'À rembourser :',
        'qty'       => 'Qté',
        'price'     => 'Prix',
        'backspace' => 'Retour arrière',

        'max-exceeded' => [
            'title' => 'Maximum dépassé',
            'body'  => 'La quantité à rembourser est supérieure à la quantité commandée. :requested demandé(s) alors que seulement :max peut être remboursé.',
        ],
    ],

    'actions' => [
        'print'    => 'Imprimer le reçu',
        'back'     => 'Retour',
        'details'  => 'Détails',
        'refund'   => 'Avoir',
        'previous' => 'Page précédente',
        'next'     => 'Page suivante',

        'refund-notification' => [
            'title' => 'Avoir créé',
            'body'  => 'L\'avoir :order est prêt à être réglé.',
        ],

        'cancel' => [
            'label'       => 'Annuler',
            'heading'     => 'Annuler cette commande ?',
            'description' => 'La commande est clôturée sans paiement et ne bloque plus la fermeture de la caisse. Cette action est irréversible.',

            'notification' => [
                'title' => 'Commande annulée',
                'body'  => ':order a été annulée.',
            ],
        ],

        'invoice' => [
            'label'   => 'Facture',
            'heading' => 'Créer une facture pour cette commande ?',

            'notification' => [
                'title' => 'Facture créée',
            ],
        ],
    ],

    'empty' => [
        'heading'     => 'Aucune commande pour l\'instant',
        'description' => 'Les commandes passées pendant la session ouverte apparaîtront ici.',
    ],
];
