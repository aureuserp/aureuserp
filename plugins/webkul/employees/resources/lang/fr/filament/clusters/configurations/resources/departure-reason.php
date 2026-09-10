<?php

return [
    'title' => 'Motifs de départ',

    'navigation' => [
        'title' => 'Motifs de départ',
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
            'name' => 'Nom',
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
                    'title' => 'Motif de départ mis à jour',
                    'body'  => 'Le motif de départ a été mis à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Motif de départ supprimé',
                    'body'  => 'Le motif de départ a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Motifs de départ supprimés',
                    'body'  => 'Les motifs de départ ont été supprimés avec succès.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Motif de départ créé',
                    'body'  => 'Le motif de départ a été créé avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nom',
    ],
];
