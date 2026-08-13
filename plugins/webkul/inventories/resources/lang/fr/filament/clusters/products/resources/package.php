<?php

return [
    'navigation' => [
        'title' => 'Colis',
        'group' => 'Inventaire',
    ],

    'global-search' => [
        'name'         => 'Nom',
        'package-type' => 'Type de colis',
        'location'     => 'Emplacement',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'name'             => 'Nom',
                    'name-placeholder' => 'ex. PACK007',
                    'package-type'     => 'Type de colis',
                    'pack-date'        => 'Date de conditionnement',
                    'location'         => 'Emplacement',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nom',
            'package-type' => 'Type de colis',
            'location'     => 'Emplacement',
            'company'      => 'Société',
            'created-at'   => 'Créé le',
            'updated-at'   => 'Mis à jour le',
        ],

        'groups' => [
            'package-type'   => 'Type de colis',
            'location'       => 'Emplacement',
            'created-at'     => 'Créé le',
        ],

        'filters' => [
            'package-type' => 'Type de colis',
            'location'     => 'Emplacement',
            'creator'      => 'Créateur',
            'company'      => 'Société',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Colis supprimé',
                        'body'  => 'Le colis a été supprimé avec succès.',
                    ],

                    'error' => [
                        'title' => 'Le colis n\'a pas pu être supprimé',
                        'body'  => 'Le colis ne peut pas être supprimé car il est actuellement utilisé.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print-without-content' => [
                'label' => 'Imprimer le code-barres',
            ],

            'print-with-content' => [
                'label' => 'Imprimer le code-barres avec contenu',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Colis supprimés',
                        'body'  => 'Les colis ont été supprimés avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les colis n\'ont pas pu être supprimés',
                        'body'  => 'Les colis ne peuvent pas être supprimés car ils sont actuellement utilisés.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Détails du colis',

                'entries' => [
                    'name'         => 'Nom du colis',
                    'package-type' => 'Type de colis',
                    'pack-date'    => 'Date de conditionnement',
                    'location'     => 'Emplacement',
                    'company'      => 'Société',
                    'created-at'   => 'Créé le',
                    'updated-at'   => 'Dernière mise à jour',
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
