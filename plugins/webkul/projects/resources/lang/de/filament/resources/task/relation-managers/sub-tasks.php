<?php

return [
    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ajouter une Sous-Tâche',

                'notification' => [
                    'title' => 'Tâche créée',
                    'body'  => 'La tâche a été créée avec succès.',
                ],
            ],
        ],

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
];
