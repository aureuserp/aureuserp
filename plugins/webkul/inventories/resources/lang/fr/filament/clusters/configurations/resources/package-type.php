<?php

return [
    'navigation' => [
        'title' => 'Types de colis',
        'group' => 'Livraison',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'fields' => [
                    'name'       => 'Nom',
                    'barcode'    => 'Code-barres',
                    'company'    => 'Société',
                    'weight'     => 'Poids',
                    'max-weight' => 'Poids maximum',

                    'fieldsets' => [
                        'size' => [
                            'title' => 'Taille',

                            'fields' => [
                                'length' => 'Longueur',
                                'width'  => 'Largeur',
                                'height' => 'Hauteur',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'barcode'    => 'Code-barres',
            'weight'     => 'Poids',
            'max-weight' => 'Poids maximum',
            'width'      => 'Largeur',
            'height'     => 'Hauteur',
            'length'     => 'Longueur',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'groups' => [
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Type de colis supprimé',
                    'body'  => 'Le type de colis a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Type de colis supprimé',
                    'body'  => 'Le type de colis a été supprimé avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Informations générales',
                'entries' => [
                    'name'      => 'Nom',
                    'fieldsets' => [
                        'size' => [
                            'title'   => 'Dimensions du colis',
                            'entries' => [
                                'length' => 'Longueur',
                                'width'  => 'Largeur',
                                'height' => 'Hauteur',
                            ],
                        ],
                    ],
                    'weight'     => 'Poids de base',
                    'max-weight' => 'Poids maximum',
                    'barcode'    => 'Code-barres',
                    'company'    => 'Société',
                    'created-at' => 'Créé le',
                    'updated-at' => 'Dernière mise à jour',
                ],
            ],

            'record-information' => [
                'title' => 'Informations sur l\'enregistrement',

                'entries' => [
                    'created-by'   => 'Créé par',
                    'created-at'   => 'Créé le',
                    'last-updated' => 'Dernière mise à jour',
                ],
            ],
        ],
    ],
];
