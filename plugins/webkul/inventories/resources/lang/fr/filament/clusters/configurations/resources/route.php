<?php

return [
    'navigation' => [
        'title' => 'Routes',
        'group' => 'Gestion des entrepôts',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'fields' => [
                    'route'             => 'Route',
                    'route-placeholder' => 'ex. Réception en deux étapes',
                    'company'           => 'Société',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Applicable sur',
                'description' => 'Choisissez les emplacements où cette route peut être appliquée.',

                'fields' => [
                    'products'                        => 'Produits',
                    'products-hint-tooltip'           => 'Si sélectionné, cette route sera disponible pour la sélection sur le produit.',
                    'product-categories'              => 'Catégories de produits',
                    'product-categories-hint-tooltip' => 'Si sélectionné, cette route sera disponible pour la sélection sur la catégorie de produit.',
                    'warehouses'                      => 'Entrepôts',
                    'warehouses-hint-tooltip'         => "Lorsqu'un entrepôt est assigné à cette route, elle sera considérée comme la route par défaut pour les produits transitant par cet entrepôt.",
                    'packaging'                       => 'Conditionnement',
                    'packaging-hint-tooltip'          => 'Si sélectionné, cette route sera disponible pour la sélection sur le conditionnement.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'route'      => 'Route',
            'company'    => 'Société',
            'deleted-at' => 'Supprimé le',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'groups' => [
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'filters' => [
            'company'  => 'Société',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Route mise à jour',
                    'body'  => 'La route a été mise à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Route restaurée',
                    'body'  => 'La route a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Route supprimée',
                    'body'  => 'La route a été supprimée avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Route définitivement supprimée',
                        'body'  => 'La route a été définitivement supprimée avec succès.',
                    ],

                    'error' => [
                        'title' => 'La route n\'a pas pu être supprimée',
                        'body'  => 'La route ne peut pas être supprimée car elle est actuellement utilisée.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Routes restaurées',
                    'body'  => 'Les routes ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Routes supprimées',
                    'body'  => 'Les routes ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Routes définitivement supprimées',
                        'body'  => 'Les routes ont été définitivement supprimées avec succès.',
                    ],

                    'error' => [
                        'title' => "Les routes n'ont pas pu être supprimées",
                        'body'  => 'Les routes ne peuvent pas être supprimées car elles sont actuellement utilisées.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'entries' => [
                    'route'             => 'Route',
                    'route-placeholder' => 'ex. Réception en deux étapes',
                    'company'           => 'Société',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Applicable sur',
                'description' => 'Sélectionnez les endroits où cette route peut être sélectionnée.',

                'entries' => [
                    'products'                        => 'Produits',
                    'products-hint-tooltip'           => 'Si sélectionné, cette route sera disponible pour la sélection sur le produit.',
                    'product-categories'              => 'Catégories de produits',
                    'product-categories-hint-tooltip' => 'Si sélectionné, cette route sera disponible pour la sélection sur la catégorie de produit.',
                    'warehouses'                      => 'Entrepôts',
                    'warehouses-hint-tooltip'         => "Lorsqu'un entrepôt est assigné à cette route, elle sera considérée comme la route par défaut pour les produits transitant par cet entrepôt.",
                    'packaging'                       => 'Conditionnement',
                    'packaging-hint-tooltip'          => 'Si sélectionné, cette route sera disponible pour la sélection sur le conditionnement.',
                ],
            ],

            'record-information' => [
                'title' => "Informations sur l'enregistrement",

                'entries' => [
                    'created-by'   => 'Créé par',
                    'created-at'   => 'Créé le',
                    'last-updated' => 'Dernière mise à jour',
                ],
            ],
        ],
    ],
];
