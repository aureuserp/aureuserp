<?php

return [
    'title' => 'Types d\'emploi',

    'navigation' => [
        'title' => 'Types d\'emploi',
        'group' => 'Recrutement',
    ],

    'form' => [
        'fields' => [
            'name'    => 'Type d\'emploi',
            'code'    => 'Code',
            'country' => 'Pays',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Type d\'emploi',
            'code'       => 'Code',
            'country'    => 'Pays',
            'created-by' => 'Créé par',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'filters' => [
            'name'       => 'Type d\'emploi',
            'country'    => 'Pays',
            'created-by' => 'Créé par',
            'updated-at' => 'Mis à jour le',
            'created-at' => 'Créé le',
        ],

        'groups' => [
            'name'       => 'Type d\'emploi',
            'country'    => 'Pays',
            'code'       => 'Code',
            'created-by' => 'Créé par',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Type d\'emploi',
                    'body'  => 'Le type d\'emploi a été modifié avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Type d\'emploi supprimé',
                    'body'  => 'Le type d\'emploi a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Types d\'emploi supprimés',
                    'body'  => 'Les types d\'emploi ont été supprimés avec succès.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Types d\'emploi',
                    'body'  => 'Les types d\'emploi ont été créés avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'    => 'Type d\'emploi',
            'code'    => 'Code',
            'country' => 'Pays',
        ],
    ],
];
