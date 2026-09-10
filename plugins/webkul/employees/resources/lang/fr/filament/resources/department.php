<?php

return [
    'title' => 'Départements',

    'navigation' => [
        'title' => 'Départements',
    ],

    'global-search' => [
        'department-manager' => 'Responsable',
        'company'            => 'Société',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informations générales',

                'fields' => [
                    'name'                => 'Nom',
                    'manager'             => 'Responsable',
                    'parent-department'   => 'Département parent',
                    'manager-placeholder' => 'Sélectionner un responsable',
                    'company'             => 'Société',
                    'company-placeholder' => 'Sélectionner une société',
                    'color'               => 'Couleur',
                ],
            ],

            'additional' => [
                'title'       => 'Informations supplémentaires',
                'description' => 'Informations supplémentaires sur ce département.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nom',
            'manager-name' => 'Responsable',
            'company-name' => 'Société',
        ],

        'groups' => [
            'name'       => 'Nom',
            'manager'    => 'Responsable',
            'company'    => 'Société',
            'updated-at' => 'Mis à jour le',
            'created-at' => 'Créé le',
        ],

        'filters' => [
            'name'         => 'Nom',
            'manager-name' => 'Responsable',
            'company-name' => 'Société',
            'updated-at'   => 'Mis à jour le',
            'created-at'   => 'Créé le',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Département restauré',
                    'body'  => 'Le département a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Département supprimé',
                    'body'  => 'Le département a été supprimé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Département supprimé définitivement',
                    'body'  => 'Le département a été supprimé définitivement avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Départements restaurés',
                    'body'  => 'Les départements ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Départements supprimés',
                    'body'  => 'Les départements ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Départements supprimés définitivement',
                    'body'  => 'Les départements ont été supprimés définitivement avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'entries' => [
                    'name'            => 'Nom',
                    'manager'         => 'Responsable',
                    'company'         => 'Société',
                    'color'           => 'Couleur',
                    'hierarchy-title' => 'Organisation du département',
                ],
            ],
        ],
    ],
];
