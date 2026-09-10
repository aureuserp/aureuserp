<?php

return [
    'title' => 'Équipes commerciales',

    'navigation' => [
        'title' => 'Équipes commerciales',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'     => 'Équipe commerciale',
                'status'   => 'Statut',
                'fieldset' => [
                    'team-details' => [
                        'title'  => 'Détails de l\'équipe',
                        'fields' => [
                            'team-leader'            => 'Responsable d\'équipe',
                            'company'                => 'Société',
                            'invoiced-target'        => 'Objectif facturé',
                            'invoiced-target-suffix' => '/ Mois',
                            'color'                  => 'Couleur',
                            'members'                => 'Membres',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'company'         => 'Société',
            'team-leader'     => 'Responsable d\'équipe',
            'name'            => 'Nom',
            'status'          => 'Statut',
            'invoiced-target' => 'Objectif facturé',
            'color'           => 'Couleur',
            'created-by'      => 'Créé par',
            'created-at'      => 'Créé le',
            'updated-at'      => 'Mis à jour le',
        ],

        'filters' => [
            'name'        => 'Nom',
            'team-leader' => 'Responsable d\'équipe',
            'company'     => 'Société',
            'created-by'  => 'Créé par',
            'updated-at'  => 'Mis à jour le',
            'created-at'  => 'Créé le',
        ],

        'groups' => [
            'name'        => 'Nom',
            'company'     => ' Société',
            'team-leader' => 'Responsable d\'équipe',
            'created-at'  => 'Créé le',
            'updated-at'  => 'Mis à jour le',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Équipe commerciale restaurée',
                    'body'  => 'L\'équipe commerciale a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Équipe commerciale supprimée',
                    'body'  => 'L\'équipe commerciale a été supprimée avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Équipe commerciale définitivement supprimée',
                    'body'  => 'L\'équipe commerciale a été définitivement supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Équipes commerciales restaurées',
                    'body'  => 'Les équipes commerciales ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Équipes commerciales supprimées',
                    'body'  => 'Les équipes commerciales ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Équipes commerciales définitivement supprimées',
                    'body'  => 'Les équipes commerciales ont été définitivement supprimées avec succès.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Équipes commerciales créées',
                    'body'  => 'Les équipes commerciales ont été créées avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'     => 'Équipe commerciale',
                'status'   => 'Statut',
                'fieldset' => [
                    'team-details' => [
                        'title'   => 'Détails de l\'équipe',
                        'entries' => [
                            'team-leader'            => 'Responsable d\'équipe',
                            'company'                => 'Société',
                            'invoiced-target'        => 'Objectif facturé',
                            'invoiced-target-suffix' => '/ Mois',
                            'color'                  => 'Couleur',
                            'members'                => 'Membres',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
