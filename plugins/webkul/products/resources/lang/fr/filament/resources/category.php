<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'fields' => [
                    'name'             => 'Nom',
                    'name-placeholder' => 'ex. Lampes',
                    'parent'           => 'Parent',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Nom',
            'full-name'   => 'Nom complet',
            'parent-path' => 'Chemin parent',
            'parent'      => 'Parent',
            'creator'     => 'Créateur',
            'created-at'  => 'Créé le',
            'created-at'  => 'Créé le',
            'updated-at'  => 'Mis à jour le',
        ],

        'groups' => [
            'parent'     => 'Parent',
            'creator'    => 'Créateur',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'filters' => [
            'parent'  => 'Parent',
            'creator' => 'Créateur',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Catégorie supprimée',
                        'body'  => 'La catégorie a été supprimée avec succès.',
                    ],

                    'error' => [
                        'title' => 'La catégorie n\'a pas pu être supprimée',
                        'body'  => 'La catégorie ne peut pas être supprimée car elle est actuellement utilisée.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Catégories supprimées',
                        'body'  => 'Les catégories ont été supprimées avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les catégories n\'ont pas pu être supprimées',
                        'body'  => 'Les catégories ne peuvent pas être supprimées car elles sont actuellement utilisées.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informations générales',

                'entries' => [
                    'name'        => 'Nom',
                    'parent'      => 'Catégorie parente',
                    'full_name'   => 'Nom complet de la catégorie',
                    'parent_path' => 'Chemin de la catégorie',
                ],
            ],

            'record-information' => [
                'title' => 'Informations sur l\'enregistrement',

                'entries' => [
                    'creator'    => 'Créé par',
                    'created_at' => 'Créé le',
                    'updated_at' => 'Dernière mise à jour le',
                ],
            ],
        ],
    ],
];
