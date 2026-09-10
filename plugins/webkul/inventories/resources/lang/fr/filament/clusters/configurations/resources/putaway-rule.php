<?php

return [
    'navigation' => [
        'title' => 'Règles de rangement',
        'group' => 'Gestion d\'entrepôt',
    ],

    'form' => [
        'fields' => [
            'in-location'          => 'Lorsque le produit arrive dans',
            'product'              => 'Produit',
            'product-placeholder'  => 'Tous les produits',
            'category'             => 'Catégorie de produit',
            'category-placeholder' => 'Toutes les catégories',
            'storage-category'     => 'Catégorie de stockage',
            'out-location'         => 'Stocker vers',
            'sub-location'         => 'Sous-emplacement',
            'company'              => 'Société',
        ],
    ],

    'table' => [
        'columns' => [
            'in-location'      => 'Lorsque le produit arrive dans',
            'product'          => 'Produit',
            'category'         => 'Catégorie de produit',
            'storage-category' => 'Catégorie de stockage',
            'out-location'     => 'Stocker vers',
            'sub-location'     => 'Sous-emplacement',
            'company'          => 'Société',
            'deleted-at'       => 'Supprimé le',
            'created-at'       => 'Créé le',
            'updated-at'       => 'Mis à jour le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Règle de rangement mise à jour',
                    'body'  => 'La règle de rangement a été mise à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Règle de rangement restaurée',
                    'body'  => 'La règle de rangement a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Règle de rangement supprimée',
                    'body'  => 'La règle de rangement a été supprimée avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Impossible de supprimer la règle de rangement',
                        'body'  => 'La règle de rangement ne peut pas être définitivement supprimée car elle est référencée par d\'autres enregistrements.',
                    ],

                    'success' => [
                        'title' => 'Règle de rangement définitivement supprimée',
                        'body'  => 'La règle de rangement a été définitivement supprimée avec succès.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Règles de rangement restaurées',
                    'body'  => 'Les règles de rangement ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Règles de rangement supprimées',
                    'body'  => 'Les règles de rangement ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Impossible de supprimer les règles de rangement',
                        'body'  => 'Certaines règles de rangement ne peuvent pas être définitivement supprimées car elles sont référencées par d\'autres enregistrements.',
                    ],

                    'success' => [
                        'title' => 'Règles de rangement définitivement supprimées',
                        'body'  => 'Les règles de rangement ont été définitivement supprimées avec succès.',
                    ],
                ],
            ],
        ],
    ],
];
