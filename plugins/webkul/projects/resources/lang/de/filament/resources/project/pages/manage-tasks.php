<?php

return [
    'title' => 'Tâches',

    'header-actions' => [
        'create' => [
            'label' => 'Nouvelle Tâche',
        ],
    ],

    'table' => [
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tâche restaurée',
                    'body'  => 'La tâche a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tâche supprimée',
                    'body'  => 'La tâche a été supprimée avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tâche supprimée définitivement',
                    'body'  => 'La tâche a été supprimée définitivement avec succès.',
                ],
            ],
        ],
    ],

    'tabs' => [
        'open-tasks'       => 'Tâches Ouvertes',
        'my-tasks'         => 'Mes Tâches',
        'unassigned-tasks' => 'Tâches Non Assignées',
        'closed-tasks'     => 'Tâches Fermées',
        'starred-tasks'    => 'Tâches Marquées',
        'archived-tasks'   => 'Tâches Archivées',
    ],
];
