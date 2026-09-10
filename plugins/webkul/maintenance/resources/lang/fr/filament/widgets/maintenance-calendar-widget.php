<?php

return [
    'heading' => [
        'title' => 'Calendrier de maintenance',
    ],

    'config' => [
        'button-text' => [
            'today' => 'Aujourd\'hui',
            'year'  => 'Année',
            'month' => 'Mois',
            'week'  => 'Semaine',
            'list'  => 'Liste',
        ],
    ],

    'header-actions' => [
        'create' => [
            'label'         => 'Nouvelle demande',
            'modal-heading' => 'Nouvelle demande de maintenance',
            'notification'  => [
                'success' => [
                    'title' => 'Demande de maintenance créée',
                    'body'  => 'La demande de maintenance a été créée avec succès.',
                ],
                'error' => [
                    'title' => 'La demande de maintenance n\'a pas pu être créée',
                    'body'  => 'Créez d\'abord une étape et une équipe de maintenance.',
                ],
            ],
        ],
    ],

    'view-action' => [
        'label' => 'Voir',
    ],

    'modal-actions' => [
        'edit' => [
            'label' => 'Modifier',
        ],
    ],

    'form' => [
        'fields' => [
            'subject'      => 'Sujet',
            'scheduled-at' => 'Planifié le',
        ],
    ],

    'infolist' => [
        'title'   => 'Demande de maintenance',
        'entries' => [
            'subject'          => 'Sujet',
            'date'             => 'Date',
            'time'             => 'Heure',
            'technician'       => 'Technicien',
            'priority'         => 'Priorité',
            'maintenance-type' => 'Type de maintenance',
            'stage'            => 'Étape',
        ],
    ],
];
