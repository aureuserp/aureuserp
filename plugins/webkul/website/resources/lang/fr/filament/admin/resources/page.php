<?php

return [
    'navigation' => [
        'title' => 'Pages',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'title'             => 'Titre',
                    'title-placeholder' => 'Titre de la page ...',
                    'slug'              => 'Slug',
                    'content'           => 'Contenu',
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
                    'is-header-visible' => 'Le menu d\'en-tête est visible',
                    'is-footer-visible' => 'Le menu de pied de page est visible',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'             => 'Titre',
            'slug'              => 'Slug',
            'creator'           => 'Créé par',
            'is-published'      => 'Est publiée',
            'is-header-visible' => 'Le menu d\'en-tête est visible',
            'is-footer-visible' => 'Le menu de pied de page est visible',
            'created-at'        => 'Créée le',
            'updated-at'        => 'Mise à jour le',
        ],

        'groups' => [
            'created-at' => 'Créée le',
        ],

        'filters' => [
            'is-published' => 'Est publiée',
            'creator'      => 'Créé par',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Page mise à jour',
                    'body'  => 'La page a été mise à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Page restaurée',
                    'body'  => 'La page a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Page supprimée',
                    'body'  => 'La page a été supprimée avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Page supprimée définitivement',
                    'body'  => 'La page a été supprimée définitivement avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Pages restaurées',
                    'body'  => 'Les pages ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Pages supprimées',
                    'body'  => 'Les pages ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Pages supprimées définitivement',
                    'body'  => 'Les pages ont été supprimées définitivement avec succès.',
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
                    'published-at'    => 'Publiée le',
                    'last-updated-by' => 'Dernière mise à jour par',
                    'last-updated'    => 'Dernière mise à jour le',
                    'created-at'      => 'Créée le',
                ],
            ],

            'settings' => [
                'title' => 'Paramètres',

                'entries' => [
                    'is-header-visible' => 'Le menu d\'en-tête est visible',
                    'is-footer-visible' => 'Le menu de pied de page est visible',
                ],
            ],
        ],
    ],
];
