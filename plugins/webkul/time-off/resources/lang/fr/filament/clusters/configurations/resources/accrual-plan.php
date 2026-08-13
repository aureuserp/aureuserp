<?php

return [
    'title'      => 'Plan d\'acquisition',
    'navigation' => [
        'title' => 'Plan d\'acquisition',
    ],

    'form' => [
        'fields' => [
            'name'                    => 'Titre',
            'is-based-on-worked-time' => 'Est basé sur le temps travaillé',
            'accrued-gain-time'       => 'Temps de gain acquis',
            'carry-over-time'         => 'Temps de report',
            'carry-over-date'         => 'Date de report',
            'status'                  => 'Statut',
        ],
    ],

    'table' => [
        'columns' => [
            'name'   => 'Nom',
            'levels' => 'Niveaux',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Plan d\'acquisition supprimé',
                    'body'  => 'Le plan d\'acquisition a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Plan d\'acquisition supprimé',
                    'body'  => 'Le plan d\'acquisition a été supprimé avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => 'Informations de base',
        ],

        'entries' => [
            'name'                    => 'Nom',
            'is-based-on-worked-time' => 'Est basé sur le temps travaillé',
            'accrued-gain-time'       => 'Temps de gain acquis',
            'carry-over-time'         => 'Temps de report',
            'carry-over-day'          => 'Jour de report',
            'carry-over-month'        => 'Mois de report',
        ],
    ],
];
