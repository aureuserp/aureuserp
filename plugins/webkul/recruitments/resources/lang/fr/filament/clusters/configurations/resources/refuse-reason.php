<?php

return [
    'title' => 'Motif de refus',

    'navigation' => [
        'title' => 'Motifs de refus',
        'group' => 'Candidatures',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nom',
            'template'         => [
                'title'                    => 'Modèle',
                'applicant-refuse'         => 'Candidat refusé',
                'applicant-not-interested' => 'Candidat non intéressé',
            ],
            'name-placeholder' => 'Saisissez le nom du motif de refus',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Nom',
            'template'   => 'Modèle',
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
                    'title' => 'Motif de refus mis à jour',
                    'body'  => 'Le motif de refus a été mis à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Motif de refus supprimé',
                    'body'  => 'Le motif de refus a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Motifs de refus supprimés',
                    'body'  => 'Les motifs de refus ont été supprimés avec succès.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Motif de refus créé',
                    'body'  => 'Le motif de refus a été créé avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'       => 'Nom',
        'template'   => 'Modèle',
    ],
];
