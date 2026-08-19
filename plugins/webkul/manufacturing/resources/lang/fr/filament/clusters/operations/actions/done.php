<?php

return [
    'label'         => 'Tout produire',
    'partial-label' => 'Produire',

    'modal' => [
        'consumption-warning' => [
            'heading'     => 'Avertissement de consommation',
            'description' => 'Certains produits ont consommé une quantité différente de celle prévue. Voulez-vous valider l\'ordre de fabrication avec les quantités actuelles ?',

            'form' => [
                'product'    => 'Produit',
                'to-consume' => 'À consommer',
                'consumed'   => 'Consommé',
                'uom'        => 'Unité de mesure',
            ],

            'actions' => [
                'confirm' => [
                    'label' => 'Confirmer',
                ],

                'set-quantities' => [
                    'label' => 'Définir les quantités et confirmer',
                ],
            ],
        ],

        'produced-warning' => [
            'heading'     => 'La quantité produite est différente de celle prévue',
            'description' => 'La quantité produite est différente de celle prévue. Voulez-vous confirmer l\'ordre de fabrication avec la quantité actuelle ?',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Ordre de fabrication terminé',
            'body'  => 'L\'ordre de fabrication a été terminé avec succès.',
        ],
    ],
];
