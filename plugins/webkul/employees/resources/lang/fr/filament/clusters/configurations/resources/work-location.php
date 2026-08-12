<?php

return [
    'title' => 'Lieux de travail',

    'navigation' => [
        'title' => 'Lieux de travail',
        'group' => 'Employé',
    ],

    'form' => [
        'name'            => 'Nom',
        'company'         => 'Société',
        'location-type'   => 'Type de lieu',
        'location-number' => 'Numéro de lieu',
        'status'          => 'Statut',
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'name'            => 'Nom',
            'status'          => 'Statut',
            'company'         => 'Société',
            'location-type'   => 'Type de lieu',
            'location-number' => 'Numéro de lieu',
            'deleted-at'      => 'Supprimé le',
            'created-by'      => 'Créé par',
            'created-at'      => 'Créé le',
            'updated-at'      => 'Mis à jour le',
        ],

        'filters' => [
            'name'            => 'Nom',
            'status'          => 'Statut',
            'created-by'      => 'Créé par',
            'company'         => 'Société',
            'location-number' => 'Numéro de lieu',
            'location-type'   => 'Type de lieu',
            'updated-at'      => 'Mis à jour le',
            'created-at'      => 'Créé le',
        ],

        'groups' => [
            'name'          => 'Nom',
            'status'        => 'Statut',
            'location-type' => 'Type de lieu',
            'company'       => 'Société',
            'created-by'    => 'Créé par',
            'created-at'    => 'Créé le',
            'updated-at'    => 'Mis à jour le',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Lieu de travail mis à jour',
                    'body'  => 'Le lieu de travail a été mis à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Lieu de travail restauré',
                    'body'  => 'Le lieu de travail a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Lieu de travail supprimé',
                    'body'  => 'Le lieu de travail a été supprimé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Lieu de travail supprimé définitivement',
                    'body'  => 'Le lieu de travail a été supprimé définitivement avec succès.',
                ],
            ],

            'empty-state' => [
                'notification' => [
                    'title' => 'Lieu de travail créé',
                    'body'  => 'Le lieu de travail a été créé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Lieux de travail supprimés',
                    'body'  => 'Les lieux de travail ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Lieux de travail supprimés définitivement',
                    'body'  => 'Les lieux de travail ont été supprimés définitivement avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'            => 'Nom',
        'company'         => 'Société',
        'location-type'   => 'Type de lieu',
        'location-number' => 'Numéro de lieu',
        'status'          => 'Statut',
    ],
];
