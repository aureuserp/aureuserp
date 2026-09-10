<?php

return [
    'notification' => [
        'title' => 'Produit mis à jour',
        'body'  => 'Le produit a été mis à jour avec succès.',
    ],

    'header-actions' => [
        'update-quantity' => [
            'label'                     => 'Mettre à jour la quantité',
            'modal-heading'             => 'Mettre à jour la quantité du produit',
            'modal-submit-action-label' => 'Mettre à jour',

            'form' => [
                'fields' => [
                    'on-hand-qty' => 'Quantité en stock',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Produit supprimé',
                'body'  => 'Le produit a été supprimé avec succès.',
            ],
        ],
    ],
];
