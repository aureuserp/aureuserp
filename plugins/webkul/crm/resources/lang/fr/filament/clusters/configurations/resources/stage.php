<?php

return [
    'model-label'        => 'Étape',
    'plural-model-label' => 'Étapes',

    'navigation' => [
        'title' => 'Étapes',
        'group' => 'Pipeline',
    ],

    'form' => [
        'fields' => [
            'teams'                    => 'Équipes commerciales',
            'teams-helper-text'        => 'Équipes dont le pipeline affiche cette étape. Laissez vide pour la partager avec toutes les équipes.',
            'all-teams'                => 'Toutes les équipes',
            'name'                     => 'Nom de l\'étape',
            'is-won'                   => 'Étape gagnée ?',
            'is-won-helper-text'       => 'Les opportunités déplacées vers cette étape sont marquées comme gagnées (probabilité 100 %).',
            'requirements'             => 'Exigences',
            'requirements-helper-text' => 'Consignes internes sur ce qui doit être fait avant de déplacer une opportunité vers cette étape.',
        ],
    ],

    'table' => [
        'columns' => [
            'teams'         => 'Équipes commerciales',
            'name'          => 'Nom de l\'étape',
            'is-won'        => 'Étape gagnée',
            'requirements'  => 'Exigences',
            'opportunities' => 'Opportunités',
            'created-at'    => 'Créé le',
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
    ],
];
