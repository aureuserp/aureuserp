<?php

return [
    'title' => 'Allocation',

    'model-label' => 'Allocation',

    'navigation' => [
        'title' => 'Allocation',
    ],

    'global-search' => [
        'employee'      => 'Employé',
        'time-off-type' => 'Type de congé',
        'date-from'     => 'Date de début',
        'date-to'       => 'Date de fin',
    ],

    'form' => [
        'fields' => [
            'name'                => 'Nom',
            'name-placeholder'    => 'Type de congé (du début à la fin de validité/sans limite)',
            'time-off-type'       => 'Type de congé',
            'employee-name'       => 'Nom de l\'employé',
            'allocation-type'     => 'Type d\'allocation',
            'validity-period'     => 'Période de validité',
            'date-from'           => 'Date de début',
            'date-to'             => 'Date de fin',
            'date-to-placeholder' => 'Sans limite',
            'allocation'          => 'Allocation',
            'allocation-suffix'   => 'Nombre de jours',
            'reason'              => 'Motif',
        ],
    ],

    'table' => [
        'columns' => [
            'employee-name'   => 'Employé',
            'time-off-type'   => 'Type de congé',
            'amount'          => 'Montant',
            'allocation-type' => 'Type d\'allocation',
            'status'          => 'Statut',
        ],

        'groups' => [
            'time-off-type'   => 'Type de congé',
            'employee-name'   => 'Nom de l\'employé',
            'allocation-type' => 'Type d\'allocation',
            'status'          => 'Statut',
            'start-date'      => 'Date de début',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Valider',
                    'approve'  => 'Approuver',
                ],
                'notification' => [
                    'title' => 'Allocation approuvée',
                    'body'  => 'L\'allocation approuvée a été approuvée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Allocation supprimée',
                    'body'  => 'L\'allocation a été supprimée avec succès.',
                ],
            ],

            'refused' => [
                'title'        => 'Refuser',
                'notification' => [
                    'title' => 'Allocation refusée',
                    'body'  => 'L\'allocation a été refusée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Allocations supprimées',
                    'body'  => 'Les allocations ont été supprimées avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'allocation-details' => [
                'title'   => 'Détails de l\'allocation',
                'entries' => [
                    'name'                => 'Nom',
                    'time-off-type'       => 'Type de congé',
                    'allocation-type'     => 'Type d\'allocation',
                ],
            ],

            'validity-period' => [
                'title'   => 'Période de validité',
                'entries' => [
                    'date-from' => 'Date de début',
                    'date-to'   => 'Date de fin',
                    'reason'    => 'Motif',
                ],
            ],
            'allocation-status' => [
                'title'   => 'Statut de l\'allocation',
                'entries' => [
                    'date-to-placeholder' => 'Sans limite',
                    'allocation'          => 'Nombre de jour(s)',
                    'allocation-value'    => ':days nombre de jours',
                    'state'               => 'État',
                ],
            ],
        ],
    ],
];
