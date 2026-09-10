<?php

return [
    'navigation' => [
        'title' => 'Produits',
        'group' => 'Inventaire',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'name'             => 'Nom',
                    'name-placeholder' => 'ex. T-shirt',
                    'description'      => 'Description',
                    'tags'             => 'Étiquettes',
                    'sales'            => 'Ventes',
                    'purchase'         => 'Achat',
                ],
            ],

            'invoice-policy' => [
                'title'            => 'Politique de facturation',
                'ordered-policy'   => 'Vous pouvez facturer les biens avant qu\'ils ne soient livrés.',
                'delivered-policy' => 'Facturer après livraison, sur la base des quantités livrées, et non commandées.',
            ],

            'images' => [
                'title' => 'Images',
            ],

            'settings' => [
                'title' => 'Paramètres',

                'fields' => [
                    'type'      => 'Type',
                    'reference' => 'Référence',
                    'barcode'   => 'Code-barres',
                    'category'  => 'Catégorie',
                    'company'   => 'Société',
                ],
            ],

            'category-and-tags' => [
                'title' => 'Catégorie et étiquettes',

                'fields' => [
                    'category' => 'Catégorie',
                    'tags'     => 'Étiquettes',
                ],
            ],

            'pricing' => [
                'title' => 'Tarification',

                'fields' => [
                    'price' => 'Prix',
                    'cost'  => 'Coût',
                ],
            ],

            'additional' => [
                'title' => 'Complémentaire',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Nom',
            'images'      => 'Images',
            'type'        => 'Type',
            'reference'   => 'Référence',
            'responsible' => 'Responsable',
            'barcode'     => 'Code-barres',
            'category'    => 'Catégorie',
            'company'     => 'Société',
            'price'       => 'Prix',
            'cost'        => 'Coût',
            'tags'        => 'Étiquettes',
            'deleted-at'  => 'Supprimé le',
            'created-at'  => 'Créé le',
            'updated-at'  => 'Mis à jour le',
        ],

        'groups' => [
            'type'       => 'Type',
            'category'   => 'Catégorie',
            'created-at' => 'Créé le',
        ],

        'filters' => [
            'name'        => 'Nom',
            'type'        => 'Type',
            'reference'   => 'Référence',
            'barcode'     => 'Code-barres',
            'category'    => 'Catégorie',
            'company'     => 'Société',
            'price'       => 'Prix',
            'cost'        => 'Coût',
            'is-favorite' => 'Est favori',
            'weight'      => 'Poids',
            'volume'      => 'Volume',
            'tags'        => 'Étiquettes',
            'responsible' => 'Responsable',
            'created-at'  => 'Créé le',
            'updated-at'  => 'Mis à jour le',
            'creator'     => 'Créateur',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Produit restauré',
                    'body'  => 'Le produit a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Produit supprimé',
                    'body'  => 'Le produit a été supprimé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Produit définitivement supprimé',
                    'body'  => 'Le produit a été définitivement supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Produits restaurés',
                    'body'  => 'Les produits ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Produits supprimés',
                    'body'  => 'Les produits ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Produits définitivement supprimés',
                    'body'  => 'Les produits ont été définitivement supprimés avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'entries' => [
                    'name'             => 'Nom',
                    'name-placeholder' => 'ex. T-shirt',
                    'description'      => 'Description',
                    'tags'             => 'Étiquettes',
                ],
            ],

            'images' => [
                'title' => 'Images',

                'entries' => [],
            ],

            'settings' => [
                'title' => 'Paramètres',

                'entries' => [
                    'type'      => 'Type',
                    'reference' => 'Référence',
                    'barcode'   => 'Code-barres',
                    'category'  => 'Catégorie',
                    'company'   => 'Société',
                ],
            ],

            'pricing' => [
                'title' => 'Tarification',

                'entries' => [
                    'price' => 'Prix',
                    'cost'  => 'Coût',
                ],
            ],

            'inventory' => [
                'title' => 'Inventaire',

                'entries' => [],

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

            'record-information' => [
                'title' => 'Informations sur l\'enregistrement',

                'entries' => [
                    'created-at' => 'Créé le',
                    'created-by' => 'Créé par',
                    'updated-at' => 'Mis à jour le',
                ],
            ],
        ],
    ],
];
