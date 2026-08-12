<?php

return [
    'navigation' => [
        'title' => 'Catégories',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informations générales',

                'fields' => [
                    'name'       => 'Nom',
                    'technician' => 'Responsable',
                    'company'    => 'Société',
                    'note'       => 'Note',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'technician' => 'Responsable',
            'company'    => 'Société',
            'created-at' => 'Créé le',
        ],

        'groups' => [
            'technician' => 'Responsable',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Catégorie mise à jour',
                    'body'  => 'La catégorie a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Catégorie supprimée',
                    'body'  => 'La catégorie a été supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Catégories supprimées',
                    'body'  => 'Les catégories ont été supprimées avec succès.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Catégorie créée',
                    'body'  => 'La catégorie a été créée avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informations générales',

                'entries' => [
                    'name'       => 'Nom',
                    'technician' => 'Responsable',
                    'company'    => 'Société',
                    'note'       => 'Note',
                ],
            ],
        ],
    ],
];
