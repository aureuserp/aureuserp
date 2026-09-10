<?php

return [
    'navigation' => [
        'title' => 'Catégories de stockage',
        'group' => 'Gestion des entrepôts',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'fields' => [
                    'name'               => 'Nom',
                    'allow-new-products' => 'Autoriser les nouveaux produits',
                    'max-weight'         => 'Poids maximum',
                    'company'            => 'Société',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Nom',
            'allow-new-products' => 'Autoriser les nouveaux produits',
            'max-weight'         => 'Poids maximum',
            'company'            => 'Société',
            'deleted-at'         => 'Supprimé le',
            'created-at'         => 'Créé le',
            'updated-at'         => 'Mis à jour le',
        ],

        'groups' => [
            'allow-new-products' => 'Autoriser les nouveaux produits',
            'created-at'         => 'Créé le',
            'updated-at'         => 'Mis à jour le',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Catégorie de stockage supprimée',
                    'body'  => 'La catégorie de stockage a été supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Catégories de stockage supprimées',
                    'body'  => 'Les catégories de stockage ont été supprimées avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'entries' => [
                    'name'               => 'Nom',
                    'allow-new-products' => 'Autoriser les nouveaux produits',
                    'max-weight'         => 'Poids maximum',
                    'company'            => 'Société',
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
