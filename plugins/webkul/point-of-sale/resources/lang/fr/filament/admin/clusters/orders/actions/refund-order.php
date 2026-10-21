<?php

return [
    'label'          => 'Retourner des produits',
    'refunded-label' => 'Remboursé',

    'form' => [
        'fields' => [
            'payment-method' => 'Mode de paiement de l\'avoir',
            'lines'          => 'Lignes à rembourser',
            'selected'       => 'Rembourser',
            'product'        => 'Produit',
            'refundable'     => 'Remboursable',
            'quantity'       => 'Quantité',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Avoir créé',
            'body'  => 'Un avoir a été créé pour les lignes sélectionnées.',
        ],
    ],
];
