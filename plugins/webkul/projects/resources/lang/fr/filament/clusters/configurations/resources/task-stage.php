<?php

return [
    'navigation' => [
        'title' => 'Étapes de Tâche',
    ],

    'form' => [
        'name'    => 'Nom',
        'project' => 'Projet',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'project'    => 'Projet',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à Jour le',
        ],

        'groups' => [
            'project'    => 'Projet',
            'created-at' => 'Créé le',
        ],

        'filters' => [
            'project' => 'Projet',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Étape de tâche mise à jour',
                    'body'  => 'L\'étape de tâche a été mise à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Étape de tâche restaurée',
                    'body'  => 'L\'étape de tâche a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Étape de tâche supprimée',
                    'body'  => 'L\'étape de tâche a été supprimée avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Étape de tâche supprimée définitivement',
                        'body'  => 'L\'étape de tâche a été supprimée définitivement avec succès.',
                    ],
                    'error' => [
                        'title' => 'L\'étape de tâche n\'a pas pu être supprimée',
                        'body'  => 'L\'étape de tâche ne peut pas être supprimée car elle est actuellement utilisée.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Étapes de tâche restaurées',
                    'body'  => 'Les étapes de tâche ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Étapes de tâche supprimées',
                    'body'  => 'Les étapes de tâche ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Étapes de tâche supprimées définitivement',
                    'body'  => 'Les étapes de tâche ont été supprimées définitivement avec succès.',
                ],
            ],
        ],
    ],
];
