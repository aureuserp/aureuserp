<?php

return [
    'navigation' => [
        'title' => 'Étiquettes',
        'group' => 'Blog',
    ],

    'form' => [
        'name'  => 'Nom',
        'color' => 'Couleur',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'color'      => 'Couleur',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Étiquette mise à jour',
                    'body'  => 'L\'étiquette a été mise à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Étiquette restaurée',
                    'body'  => 'L\'étiquette a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Étiquette supprimée',
                    'body'  => 'L\'étiquette a été supprimée avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Étiquette définitivement supprimée',
                    'body'  => 'L\'étiquette a été définitivement supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Étiquettes restaurées',
                    'body'  => 'Les étiquettes ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Étiquettes supprimées',
                    'body'  => 'Les étiquettes ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Étiquettes définitivement supprimées',
                    'body'  => 'Les étiquettes ont été définitivement supprimées avec succès.',
                ],
            ],
        ],
    ],
];
