<?php

return [
    'navigation' => [
        'title' => 'Plans d\'Activité',
    ],

    'form' => [
        'name'   => 'Nom',
        'status' => 'Statut',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'status'     => 'Statut',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à Jour le',
        ],

        'groups' => [
            'name'       => 'Nom',
            'status'     => 'Statut',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à Jour le',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Plan d\'activité restauré',
                    'body'  => 'Le plan d\'activité a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Plan d\'activité supprimé',
                    'body'  => 'Le plan d\'activité a été supprimé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Plan d\'activité supprimé définitivement',
                    'body'  => 'Le plan d\'activité a été supprimé définitivement avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Plans d\'activité restaurés',
                    'body'  => 'Les plans d\'activité ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Plans d\'activité supprimés',
                    'body'  => 'Les plans d\'activité ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Plans d\'activité supprimés définitivement',
                    'body'  => 'Les plans d\'activité ont été supprimés définitivement avec succès.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Plan d\'activité créé',
                    'body'  => 'Le plan d\'activité a été créé avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'   => 'Nom',
        'status' => 'Statut',
    ],
];
