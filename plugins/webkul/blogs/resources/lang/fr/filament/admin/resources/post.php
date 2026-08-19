<?php

return [
    'navigation' => [
        'title' => 'Articles de blog',
    ],

    'global-search' => [
        'author' => 'Auteur',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'title'             => 'Titre',
                    'sub-title'         => 'Sous-titre',
                    'title-placeholder' => 'Titre de l\'article ...',
                    'slug'              => 'Slug',
                    'content'           => 'Contenu',
                    'banner'            => 'Bannière',
                ],
            ],

            'seo' => [
                'title' => 'SEO',

                'fields' => [
                    'meta-title'       => 'Méta-titre',
                    'meta-keywords'    => 'Méta-mots-clés',
                    'meta-description' => 'Méta-description',
                ],
            ],

            'settings' => [
                'title' => 'Paramètres',

                'fields' => [
                    'category'     => 'Catégorie',
                    'tags'         => 'Étiquettes',
                    'name'         => 'Nom',
                    'color'        => 'Couleur',
                    'is-published' => 'Publié',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Titre',
            'slug'         => 'Slug',
            'author'       => 'Auteur',
            'category'     => 'Catégorie',
            'creator'      => 'Créé par',
            'is-published' => 'Publié',
            'created-at'   => 'Créé le',
            'updated-at'   => 'Mis à jour le',
        ],

        'groups' => [
            'category'   => 'Catégorie',
            'author'     => 'Auteur',
            'created-at' => 'Créé le',
        ],

        'filters' => [
            'is-published' => 'Publié',
            'author'       => 'Auteur',
            'creator'      => 'Créé par',
            'category'     => 'Catégorie',
            'tags'         => 'Étiquettes',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Article mis à jour',
                    'body'  => 'L\'article a été mis à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Article restauré',
                    'body'  => 'L\'article a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Article supprimé',
                    'body'  => 'L\'article a été supprimé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Article définitivement supprimé',
                    'body'  => 'L\'article a été définitivement supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Articles restaurés',
                    'body'  => 'Les articles ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Articles supprimés',
                    'body'  => 'Les articles ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Articles définitivement supprimés',
                    'body'  => 'Les articles ont été définitivement supprimés avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'entries' => [
                    'title'   => 'Titre',
                    'slug'    => 'Slug',
                    'content' => 'Contenu',
                    'banner'  => 'Bannière',
                ],
            ],

            'seo' => [
                'title' => 'SEO',

                'entries' => [
                    'meta-title'       => 'Méta-titre',
                    'meta-keywords'    => 'Méta-mots-clés',
                    'meta-description' => 'Méta-description',
                ],
            ],

            'record-information' => [
                'title' => 'Informations sur l\'enregistrement',

                'entries' => [
                    'author'          => 'Auteur',
                    'created-by'      => 'Créé par',
                    'published-at'    => 'Publié le',
                    'last-updated-by' => 'Dernière mise à jour par',
                    'last-updated'    => 'Dernière mise à jour le',
                    'created-at'      => 'Créé le',
                ],
            ],

            'settings' => [
                'title' => 'Paramètres',

                'entries' => [
                    'category'     => 'Catégorie',
                    'tags'         => 'Étiquettes',
                    'name'         => 'Nom',
                    'color'        => 'Couleur',
                    'is-published' => 'Publié',
                ],
            ],
        ],
    ],
];
