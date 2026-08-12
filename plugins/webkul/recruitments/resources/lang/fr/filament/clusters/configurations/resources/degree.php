<?php

return [
    'title' => 'Diplômes',

    'navigation' => [
        'title' => 'Diplômes',
        'group' => 'Candidatures',
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
            'name-placeholder' => 'Saisissez le nom du diplôme',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Nom',
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
                    'title' => 'Diplôme mis à jour',
                    'body'  => 'Le diplôme a été mis à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Diplôme supprimé',
                    'body'  => 'Le diplôme a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Diplômes supprimés',
                    'body'  => 'Les diplômes ont été supprimés avec succès.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Diplôme créé',
                    'body'  => 'Le diplôme a été créé avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nom',
    ],
];
