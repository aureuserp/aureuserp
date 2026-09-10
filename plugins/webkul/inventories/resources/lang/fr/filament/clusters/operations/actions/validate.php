<?php

return [
    'label'             => 'Valider',
    'modal-heading'     => 'Créer un reliquat ?',
    'modal-description' => 'Créez un reliquat si les produits restants seront traités ultérieurement. Sinon, ne générez pas de reliquat.',

    'extra-modal-footer-actions' => [
        'no-backorder' => [
            'label' => 'Aucun reliquat',
        ],
    ],

    'notification' => [
        'error' => [
            'title' => 'Échec de la validation',
        ],

        'warning' => [
            'lines-missing' => [
                'title' => 'Aucune quantité n\'est réservée',
                'body'  => 'Aucune quantité n\'est réservée pour le transfert.',
            ],

            'no-quantities-reserved' => [
                'title' => 'Aucune quantité n\'est réservée',
                'body'  => 'Aucune quantité n\'est réservée pour le transfert.',
            ],

            'lot-missing' => [
                'title' => 'Fournir un numéro de lot/série',
                'body'  => 'Vous devez fournir un numéro de lot/série pour les produits :products.',
            ],

            'serial-qty' => [
                'title' => 'Numéro de série déjà attribué',
                'body'  => 'Le numéro de série a déjà été attribué à un autre produit.',
            ],

            'partial-package' => [
                'title' => 'Impossible de déplacer le même contenu de colis',
                'body'  => 'Vous ne pouvez pas déplacer le contenu du même colis plus d\'une fois au sein d\'un même transfert, ni répartir le colis entre deux emplacements.',
            ],
        ],
    ],
];
