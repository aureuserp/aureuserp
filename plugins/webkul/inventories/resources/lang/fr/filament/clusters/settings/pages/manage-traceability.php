<?php

return [
    'title' => 'Gérer la traçabilité',

    'form' => [
        'enable-lots-serial-numbers'                             => 'Lots et numéros de série',
        'enable-lots-serial-numbers-helper-text'                 => 'Obtenez une traçabilité complète des fournisseurs aux clients',
        'configure-lots'                                         => 'Configurer les lots',
        'enable-expiration-dates'                                => 'Dates d\'expiration',
        'enable-expiration-dates-helper-text'                    => 'Définissez des dates d\'expiration sur les lots et numéros de série',
        'display-on-delivery-slips'                              => 'Afficher sur les bons de livraison',
        'display-on-delivery-slips-helper-text'                  => 'Les lots et numéros de série apparaîtront sur les bons de livraison',
        'display-expiration-dates-on-delivery-slips'             => 'Afficher les dates d\'expiration sur les bons de livraison',
        'display-expiration-dates-on-delivery-slips-helper-text' => 'Les dates d\'expiration apparaîtront sur le bon de livraison',
        'enable-consignments'                                    => 'Consignations',
        'enable-consignments-helper-text'                        => 'Définir le propriétaire sur les produits stockés',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Vous avez des produits en stock dont le suivi par lot/numéro de série est activé. ',
                'body'  => 'Désactivez d\'abord le suivi sur tous les produits avant de désactiver ce paramètre.',
            ],
        ],
    ],
];
