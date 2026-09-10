<?php

return [
    'title' => 'Congé',

    'model-label' => 'Mon congé',

    'navigation' => [
        'title' => 'Mon congé',
    ],

    'form' => [
        'fields' => [
            'time-off-type'     => 'Type de congé',
            'date'              => 'Date',
            'dates'             => 'Dates',
            'request-date-from' => 'Date de demande du',
            'request-date-to'   => 'Date de demande au',
            'description'       => 'Description',
            'period'            => 'Période',
            'half-day'          => 'Demi-journée',
            'requested-days'    => 'Demandé (Jours/Heures)',
            'description'       => 'Description',
            'attachment'        => 'Pièce jointe',
            'day'               => ':day jour',
            'days'              => ':days jour(s)',
        ],
    ],

    'table' => [
        'columns' => [
            'employee-name'  => 'Employé',
            'time-off-type'  => 'Type de congé',
            'description'    => 'Description',
            'date-from'      => 'Date de début',
            'date-to'        => 'Date de fin',
            'duration'       => 'Durée',
            'status'         => 'Statut',
        ],

        'groups' => [
            'employee-name' => 'Employé',
            'time-off-type' => 'Type de congé',
            'status'        => 'Statut',
            'start-date'    => 'Date de début',
            'start-to'      => 'Date de fin',
            'updated-at'    => 'Mis à jour le',
            'created-at'    => 'Créé le',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Valider',
                    'approve'  => 'Approuver',
                ],
                'notification' => [
                    'title' => 'Congé approuvé',
                    'body'  => 'Le congé a été approuvé avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Congé supprimé',
                    'body'  => 'Le congé a été supprimé avec succès.',
                ],
            ],

            'refused' => [
                'title'        => 'Refuser',
                'notification' => [
                    'title' => 'Congé refusé',
                    'body'  => 'Le congé a été refusé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Congés supprimés',
                    'body'  => 'Les congés ont été supprimés avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'time-off-type'     => 'Type de congé',
            'date'              => 'Date',
            'dates'             => 'Dates',
            'request-date-from' => 'Date de demande du',
            'request-date-to'   => 'Date de demande au',
            'description'       => 'Description',
            'period'            => 'Période',
            'half-day'          => 'Demi-journée',
            'requested-days'    => 'Demandé (Jours/Heures)',
            'description'       => 'Description',
            'attachment'        => 'Pièce jointe',
            'day'               => ':day jour',
            'days'              => ':days jour(s)',
        ],
    ],
];
