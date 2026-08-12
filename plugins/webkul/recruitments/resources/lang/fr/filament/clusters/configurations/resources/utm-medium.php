<?php

return [
    'title' => 'Supports',

    'navigation' => [
        'title' => 'Supports',
        'group' => 'UTM',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nom',
            'name-placeholder' => 'Saisissez le nom du support',
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
            'created-by' => 'Créé par',
            'updated-at' => 'Mis à jour le',
            'created-at' => 'Créé le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Support mis à jour',
                    'body'  => 'Le support a été mis à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Support supprimé',
                    'body'  => 'Le support a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Supports supprimés',
                    'body'  => 'Les supports ont été supprimés avec succès.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Support créé',
                    'body'  => 'Le support a été créé avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nom',
    ],
];
