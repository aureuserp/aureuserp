<?php

return [
    'before-save' => [
        'notification' => [
            'error' => [
                'tracking-update' => [
                    'title' => 'Erreur lors de la mise à jour du suivi',
                    'body'  => 'Vous ne pouvez pas modifier le suivi d\'inventaire d\'un produit déjà utilisé.',
                ],

                'reordering-rules' => [
                    'title' => 'Erreur lors de la mise à jour du produit',
                    'body'  => 'Ce produit possède encore des règles de réapprovisionnement actives. Veuillez d\'abord les archiver ou les supprimer.',
                ],

                'reserved' => [
                    'title' => 'Erreur lors de la mise à jour du suivi',
                    'body'  => 'Vous ne pouvez pas modifier le suivi d\'inventaire d\'un produit actuellement réservé sur un mouvement de stock. Si vous devez modifier le suivi d\'inventaire, vous devez d\'abord annuler la réservation du mouvement de stock.',
                ],

                'qty-not-zero' => [
                    'title' => 'Erreur lors de la mise à jour du suivi',
                    'body'  => 'La quantité disponible doit être fixée à zéro avant de modifier le suivi d\'inventaire.',
                ],

                'track-by-update' => [
                    'title' => 'Erreur lors de la mise à jour du suivi',
                    'body'  => 'Vous avez des produits en stock sans numéro de lot/série. Vous pouvez attribuer des numéros de lot/série en effectuant un ajustement d\'inventaire.',
                ],
            ],
        ],
    ],
];
