<?php

return [
    'navigation' => [
        'title' => 'Étapes',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nom',
            'done' => 'Terminé',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'done'       => 'Terminé',
            'created-at' => 'Créé le',
        ],

        'groups' => [
            'done'       => 'Terminé',
            'created-at' => 'Créé le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Étape mise à jour',
                    'body'  => 'L\'étape a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Étape supprimée',
                    'body'  => 'L\'étape a été supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Étapes supprimées',
                    'body'  => 'Les étapes ont été supprimées avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informations générales',

                'entries' => [
                    'name' => 'Nom',
                    'done' => 'Terminé',
                ],
            ],
        ],
    ],
];
