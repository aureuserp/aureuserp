<?php

return [
    'title' => 'Étiquettes',

    'navigation' => [
        'title' => 'Étiquettes',
        'group' => 'Employé',
    ],

    'groups' => [
        'status'     => 'Statut',
        'created-by' => 'Créé par',
        'created-at' => 'Créé le',
        'updated-at' => 'Mis à jour le',
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
            'created-by' => 'Créé par',
            'updated-by' => 'Mis à jour par',
            'updated-at' => 'Mis à jour le',
            'created-at' => 'Créé le',
        ],

        'groups' => [
            'name'         => 'Nom',
            'job-position' => 'Poste',
            'color'        => 'Couleur',
            'created-by'   => 'Créé par',
            'created-at'   => 'Créé le',
            'updated-at'   => 'Mis à jour le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Étiquette mise à jour',
                    'body'  => 'L\'étiquette a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Étiquette supprimée',
                    'body'  => 'L\'étiquette a été supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Étiquettes supprimées',
                    'body'  => 'Les étiquettes ont été supprimées avec succès.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Étiquette créée',
                    'body'  => 'L\'étiquette a été créée avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'  => 'Nom',
        'color' => 'Couleur',
    ],
];
