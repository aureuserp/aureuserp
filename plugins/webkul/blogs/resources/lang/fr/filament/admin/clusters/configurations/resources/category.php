<?php

return [
    'navigation' => [
        'title' => 'Catégories',
        'group' => 'Blog',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nom',
            'name-placeholder' => 'Titre de la catégorie ...',
            'sub-title'        => 'Sous-titre',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'nom',
            'sub-title'  => 'Sous-titre',
            'posts'      => 'Articles',
            'created-at' => 'Créé le',
        ],

        'filters' => [
            'is-published' => 'Publié',
            'author'       => 'Auteur',
            'creator'      => 'Créé par',
            'category'     => 'Catégorie',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Catégorie mise à jour',
                    'body'  => 'La catégorie a été mise à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Catégorie restaurée',
                    'body'  => 'La catégorie a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Catégorie supprimée',
                    'body'  => 'La catégorie a été supprimée avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Catégorie définitivement supprimée',
                        'body'  => 'La catégorie a été définitivement supprimée avec succès.',
                    ],
                    'error' => [
                        'title' => 'La catégorie  ne peut pas être supprimée',
                        'body'  => 'La catégorie  ne peut pas être supprimée car elle est actuellement utilisée.',
                    ],
                ],
            ],

            'force-delete-error' => [
                'notification' => [
                    'title' => 'Impossible de supprimer la catégorie',
                    'body'  => 'Vous ne pouvez pas supprimer cette catégorie car elle est associée à des articles.',
                ],

                'exception' => 'Vous ne pouvez pas supprimer définitivement cette catégorie car elle est associée à des articles.',
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Catégories restaurées',
                    'body'  => 'Les catégories ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Catégories supprimées',
                    'body'  => 'Les catégories ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Catégories définitivement supprimées',
                    'body'  => 'Les catégories ont été définitivement supprimées avec succès.',
                ],
            ],

            'force-delete-error' => [
                'notification' => [
                    'title' => 'Impossible de supprimer la catégorie',
                    'body'  => 'Vous ne pouvez pas supprimer cette catégorie car elle est associée à des articles.',
                ],
            ],
        ],
    ],

    'infolist' => [
    ],
];
