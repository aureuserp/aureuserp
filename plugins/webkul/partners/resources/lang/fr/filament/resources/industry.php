<?php

return [
    'form' => [
        'name'      => 'Nom',
        'full-name' => 'Nom complet',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'full-name'  => 'Nom complet',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Secteur d\'activité mis à jour',
                    'body'  => 'Le secteur d\'activité a été mis à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Secteur d\'activité restauré',
                    'body'  => 'Le secteur d\'activité a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Secteur d\'activité supprimé',
                    'body'  => 'Le secteur d\'activité a été supprimé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Secteur d\'activité définitivement supprimé',
                    'body'  => 'Le secteur d\'activité a été définitivement supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Secteurs d\'activité restaurés',
                    'body'  => 'Les secteurs d\'activité ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Secteurs d\'activité supprimés',
                    'body'  => 'Les secteurs d\'activité ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Secteurs d\'activité définitivement supprimés',
                    'body'  => 'Les secteurs d\'activité ont été définitivement supprimés avec succès.',
                ],
            ],
        ],
    ],
];
