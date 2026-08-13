<?php

return [
    'navigation' => [
        'title' => 'Étapes de Projet',
    ],

    'form' => [
        'name' => 'Nom',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à Jour le',
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
                    'title' => 'Étape de projet mise à jour',
                    'body'  => 'L\'étape de projet a été mise à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Étape de projet restaurée',
                    'body'  => 'L\'étape de projet a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Étape de projet supprimée',
                    'body'  => 'L\'étape de projet a été supprimée avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Étape de projet supprimée définitivement',
                        'body'  => 'L\'étape de projet a été supprimée définitivement avec succès.',
                    ],
                    'error' => [
                        'title' => 'L\'étape de projet n\'a pas pu être supprimée',
                        'body'  => 'L\'étape de projet ne peut pas être supprimée car elle est actuellement utilisée.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Étapes de projet restaurées',
                    'body'  => 'Les étapes de projet ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Étapes de projet supprimées',
                    'body'  => 'Les étapes de projet ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Étapes de projet supprimées définitivement',
                    'body'  => 'Les étapes de projet ont été supprimées définitivement avec succès.',
                ],
            ],
        ],
    ],
];
