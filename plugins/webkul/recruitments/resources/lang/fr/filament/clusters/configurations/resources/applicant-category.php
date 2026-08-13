<?php

return [
    'title' => 'Étiquettes',

    'navigation' => [
        'title' => 'Étiquettes',
        'group' => 'Candidatures',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nom',
            'name-placeholder' => 'Saisissez le nom de l\'étiquette',
            'color'            => 'Couleur',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Nom',
            'color'      => 'Couleur',
            'created-by' => 'Créé par',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'filters' => [
            'name'       => 'Nom',
            'employee'   => 'Employé',
            'created-by' => 'Créé par',
            'updated-at' => 'Mis à jour le',
            'created-at' => 'Créé le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Étiquettes mises à jour',
                    'body'  => 'Les étiquettes ont été mises à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Étiquettes supprimées',
                    'body'  => 'Les étiquettes ont été supprimées avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Catégories de candidats supprimées',
                    'body'  => 'Les catégories de candidats ont été supprimées avec succès.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Étiquettes créées',
                    'body'  => 'Les étiquettes ont été créées avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'Nom',
        'color' => 'Couleur',
    ],
];
