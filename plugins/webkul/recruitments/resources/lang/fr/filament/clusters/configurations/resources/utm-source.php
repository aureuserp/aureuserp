<?php

return [
    'title' => 'Sources',

    'navigation' => [
        'title' => 'Sources',
        'group' => 'UTM',
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
            'name-placeholder' => 'Saisissez le nom de la source',
            'status'           => 'Statut',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Nom',
            'status'     => 'Statut',
            'created-by' => 'Créé par',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'filters' => [
            'name'       => 'Nom',
            'created-by' => 'Créé par',
            'updated-at' => 'Mis à jour le',
            'created-at' => 'Créé le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Source mise à jour',
                    'body'  => 'La source a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Source supprimée',
                    'body'  => 'La source a été supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sources supprimées',
                    'body'  => 'Les sources ont été supprimées avec succès.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Source créée',
                    'body'  => 'La source a été créée avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nom',
    ],
];
