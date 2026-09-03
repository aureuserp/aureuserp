<?php

return [
    'navigation' => [
        'title' => 'Plans d\'activités',
        'group' => 'Activités',
    ],

    'global-search' => [
        'name'         => 'Département',
        'department'   => 'Département',
        'manager'      => 'Responsable',
        'company'      => 'Société',
        'plugin'       => 'Plugin',
        'creator-name' => 'Créé par',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'status'     => 'Statut',
            'department' => 'Département',
            'company'    => 'Société',
            'manager'    => 'Responsable',
            'created-by' => 'Créé par',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'filters' => [
            'name'           => 'Nom',
            'plugin'         => 'Plugin',
            'activity-types' => 'Types d\'activités',
            'company'        => 'Société',
            'department'     => 'Département',
            'is-active'      => 'Statut',
            'updated-at'     => 'Mis à jour le',
            'created-at'     => 'Créé le',
        ],

        'groups' => [
            'status'     => 'Statut',
            'name'       => 'Nom',
            'created-by' => 'Créé par',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
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
                    'title' => 'Plan d\'activité définitivement supprimé',
                    'body'  => 'Le plan d\'activité a été définitivement supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Plans d\'activités restaurés',
                    'body'  => 'Les plans d\'activités ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Plans d\'activités supprimés',
                    'body'  => 'Les plans d\'activités ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Plans d\'activités définitivement supprimés',
                    'body'  => 'Les plans d\'activités ont été définitivement supprimés avec succès.',
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
];
