<?php

return [
    'form' => [
        'name'    => 'Nom',
        'barcode' => 'Code-barres',
        'product' => 'Produit',
        'routes'  => 'Routes',
        'qty'     => 'Qté',
        'company' => 'Société',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'product'    => 'Produit',
            'routes'     => 'Routes',
            'qty'        => 'Qté',
            'company'    => 'Société',
            'barcode'    => 'Code-barres',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'groups' => [
            'product'    => 'Produit',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'filters' => [
            'product' => 'Produit',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Emballage mis à jour',
                    'body'  => 'L\'emballage a été mis à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Emballage supprimé',
                        'body'  => 'L\'emballage a été supprimé avec succès.',
                    ],

                    'error' => [
                        'title' => 'L\'emballage n\'a pas pu être supprimé',
                        'body'  => 'L\'emballage ne peut pas être supprimé car il est actuellement utilisé.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Imprimer',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Emballages supprimés',
                        'body'  => 'Les emballages ont été supprimés avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les emballages n\'ont pas pu être supprimés',
                        'body'  => 'Les emballages ne peuvent pas être supprimés car ils sont actuellement utilisés.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Nouvel emballage',

                'notification' => [
                    'title' => 'Emballage créé',
                    'body'  => 'L\'emballage a été créé avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informations générales',

                'entries' => [
                    'name'    => 'Nom du colis',
                    'barcode' => 'Code-barres',
                    'product' => 'Produit',
                    'qty'     => 'Quantité',
                ],
            ],

            'organization' => [
                'title' => 'Détails de l\'organisation',

                'entries' => [
                    'company'    => 'Société',
                    'creator'    => 'Créé par',
                    'created_at' => 'Créé le',
                    'updated_at' => 'Dernière mise à jour le',
                ],
            ],
        ],
    ],
];
