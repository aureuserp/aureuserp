<?php

return [
    'model-label'        => 'Étiquette',
    'plural-model-label' => 'Étiquettes',

    'navigation' => [
        'title' => 'Étiquettes',
        'group' => 'Pipeline',
    ],

    'form' => [
        'fields' => [
            'name'  => 'Nom de l\'étiquette',
            'color' => 'Couleur',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom de l\'étiquette',
            'color'      => 'Couleur',
            'created-by' => 'Créé par',
            'created-at' => 'Créé le',
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
    ],

    'infolist' => [
        'entries' => [
            'name'  => 'Nom de l\'étiquette',
            'color' => 'Couleur',
        ],
    ],
];
