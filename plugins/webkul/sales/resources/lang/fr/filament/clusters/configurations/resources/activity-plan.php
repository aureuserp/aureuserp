<?php

return [
    'navigation' => [
        'title' => 'Plans d\'activité',
        'group' => 'Activités',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Informations générales',
                'fields' => [
                    'name'       => 'Nom',
                    'status'     => 'Statut',
                    'department' => 'Département',
                    'company'    => 'Société',
                ],
            ],
        ],
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
            'activity-types' => 'Types d\'activité',
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
                    'title' => 'Plans d\'activité définitivement supprimés',
                    'body'  => 'Les plans d\'activité ont été définitivement supprimés avec succès.',
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
        'sections' => [
            'general' => [
                'title'   => 'Informations générales',
                'entries' => [
                    'name'       => 'Nom',
                    'status'     => 'Statut',
                    'department' => 'Département',
                    'manager'    => 'Responsable',
                    'company'    => 'Société',
                ],
            ],
        ],
    ],
];
