<?php

return [
    'table' => [
        'columns' => [
            'on-hand'    => 'En stock',
            'forecasted' => 'Prévu',
        ],
    ],

    'navigation' => [
        'title' => 'Produits',
        'group' => 'Inventaire',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Inventaire',

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Suivi',

                        'fields' => [
                            'track-inventory'              => 'Suivre l\'inventaire',
                            'track-inventory-hint-tooltip' => 'Un produit stockable est un produit qui nécessite une gestion des stocks..',
                            'track-by'                     => 'Suivre par',
                            'expiration-date'              => 'Date d\'expiration',
                            'expiration-date-hint-tooltip' => 'Si sélectionné, vous pouvez spécifier des dates d\'expiration pour le produit et ses numéros de lot/série associés.',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Opérations',

                        'fields' => [
                            'routes'              => 'Routes',
                            'routes-hint-tooltip' => 'Selon les modules installés, ce paramètre vous permet de définir la route du produit, telle que l\'achat, la fabrication ou le réapprovisionnement sur commande.',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Logistique',

                        'fields' => [
                            'responsible'              => 'Responsable',
                            'responsible-hint-tooltip' => 'Le délai de livraison (en jours) représente la durée promise entre la confirmation de la commande client et la livraison du produit.',
                            'weight'                   => 'Poids',
                            'volume'                   => 'Volume',
                            'sale-delay'               => 'Délai de livraison client (jours)',
                            'sale-delay-hint-tooltip'  => 'Le délai de livraison (en jours) représente la durée promise entre la confirmation de la commande client et la livraison du produit.',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Traçabilité',

                        'fields' => [
                            'expiration-date'               => 'Date d\'expiration (jours)',
                            'expiration-date-hint-tooltip'  => 'Si sélectionné, vous pouvez définir des dates d\'expiration pour le produit et ses numéros de lot/série associés.',
                            'best-before-date'              => 'Date de péremption (jours)',
                            'best-before-date-hint-tooltip' => 'Le nombre de jours avant la date d\'expiration à partir duquel le produit commence à se détériorer, bien qu\'il reste sûr à utiliser. Ceci est calculé en fonction du numéro de lot/série.',
                            'removal-date'                  => 'Date de retrait (jours)',
                            'removal-date-hint-tooltip'     => 'Le nombre de jours avant la date d\'expiration auquel le produit doit être retiré du stock. Ceci est calculé en fonction du numéro de lot/série.',
                            'alert-date'                    => 'Date d\'alerte (jours)',
                            'alert-date-hint-tooltip'       => 'Le nombre de jours avant la date d\'expiration auquel une alerte doit être déclenchée pour le numéro de lot/série. Ceci est calculé en fonction du numéro de lot/série.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Complément',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Inventaire',

                'entries' => [
                ],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Suivi',

                        'entries' => [
                            'track-inventory' => 'Suivre l\'inventaire',
                            'track-by'        => 'Suivre par',
                            'expiration-date' => 'Date d\'expiration',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Opérations',

                        'entries' => [
                            'routes' => 'Routes',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Logistique',

                        'entries' => [
                            'responsible' => 'Responsable',
                            'weight'      => 'Poids',
                            'volume'      => 'Volume',
                            'sale-delay'  => 'Délai de livraison client (jours)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Traçabilité',

                        'entries' => [
                            'expiration-date'  => 'Date d\'expiration (jours)',
                            'best-before-date' => 'Date de péremption (jours)',
                            'removal-date'     => 'Date de retrait (jours)',
                            'alert-date'       => 'Date d\'alerte (jours)',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
