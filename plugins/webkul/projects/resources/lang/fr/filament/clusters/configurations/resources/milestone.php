<?php

return [
    'navigation' => [
        'title' => 'Jalons',
    ],

    'form' => [
        'name'         => 'Nom',
        'deadline'     => 'Échéance',
        'is-completed' => 'Est Complété',
        'project'      => 'Projet',
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nom',
            'deadline'     => 'Échéance',
            'is-completed' => 'Est Complété',
            'completed-at' => 'Complété le',
            'project'      => 'Projet',
            'creator'      => 'Créateur',
            'created-at'   => 'Créé le',
            'updated-at'   => 'Mis à Jour le',
        ],

        'groups' => [
            'name'         => 'Nom',
            'is-completed' => 'Est Complété',
            'project'      => 'Projet',
            'created-at'   => 'Créé le',
        ],

        'filters' => [
            'is-completed' => 'Est Complété',
            'project'      => 'Projet',
            'creator'      => 'Créateur',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Jalon mis à jour',
                    'body'  => 'Le jalon a été mis à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Jalon supprimé',
                    'body'  => 'Le jalon a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Jalons supprimés',
                    'body'  => 'Les jalons ont été supprimés avec succès.',
                ],
            ],
        ],
    ],
];
