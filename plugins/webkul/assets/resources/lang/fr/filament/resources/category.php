<?php

return [
    'title'        => "Catégorie d'actif",
    'plural-title' => "Catégories d'actifs",

    'navigation' => [
        'title' => "Catégories d'actifs",
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Détails de la catégorie',
                'fields' => [
                    'name'   => 'Nom de la catégorie',
                    'parent' => 'Catégorie parente',
                ],
            ],

            'settings' => [
                'title'  => 'Paramètres',
                'fields' => [
                    'company' => 'Société',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Nom',
            'full-name'      => 'Chemin complet',
            'parent'         => 'Catégorie parente',
            'products-count' => 'Produits',
            'assets-count'   => 'Actifs',
            'total-cost'     => 'Coût total',
            'company'        => 'Société',
            'creator'        => 'Créé par',
            'created-at'     => 'Créé le',
            'updated-at'     => 'Mis à jour le',
        ],

        'filters' => [
            'parent'  => 'Catégorie parente',
            'company' => 'Société',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Catégorie mise à jour',
                    'body'  => 'La catégorie a été mise à jour avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Détails de la catégorie',
                'entries' => [
                    'name'           => 'Nom',
                    'full-name'      => 'Chemin complet',
                    'parent'         => 'Catégorie parente',
                    'products-count' => 'Produits',
                    'assets-count'   => 'Actifs',
                    'total-cost'     => 'Coût total',
                ],
            ],

            'audit' => [
                'title'   => 'Audit',
                'entries' => [
                    'creator'    => 'Créé par',
                    'created-at' => 'Créé le',
                    'updated-at' => 'Mis à jour le',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'category-products' => [
            'title' => 'Produits de cette catégorie',
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'Nouvelle catégorie',
                    'notification' => [
                        'title' => 'Catégorie créée',
                        'body'  => 'La catégorie a été créée avec succès.',
                    ],
                ],
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Catégorie créée',
                'body'  => 'La catégorie a été créée avec succès.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Catégorie mise à jour',
                'body'  => 'La catégorie a été mise à jour avec succès.',
            ],
        ],
    ],
];
