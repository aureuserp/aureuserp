<?php

return [
    'title' => 'Présences',

    'navigation' => [
        'title' => 'Présences',
    ],

    'form' => [
        'employee'                   => 'Employé',
        'work-date'                  => 'Date de travail',
        'duplicate-day'              => ':employee a déjà un relevé de présence le :date.',
        'check-in'                   => 'Arrivée',
        'check-in-not-in-work-date'  => "L'arrivée doit être dans la date de travail.",
        'check-out'                  => 'Départ',
        'check-out-before-check-in'  => 'Le départ doit être après l’arrivée.',
        'check-out-not-in-work-date' => 'Le départ doit être dans la date de travail.',
        'long-shift-warning'         => 'Attention : ce poste dure :hours heures.',
        'source'                     => 'Source',
        'source-manual'              => 'Manuel',
    ],

    'table' => [
        'columns' => [
            'employee'   => 'Employé',
            'work-date'  => 'Date de travail',
            'check-in'   => 'Arrivée',
            'check-out'  => 'Départ',
            'worked'     => 'Travaillé',
            'source'     => 'Source',
            'created-at' => 'Créé le',
        ],

        'filters' => [
            'employee'          => 'Employé',
            'source'            => 'Source',
            'work-date'         => 'Date de travail',
            'preset'            => 'Période rapide',
            'preset-today'      => 'Aujourd’hui',
            'preset-this-week'  => 'Cette semaine',
            'preset-this-month' => 'Ce mois-ci',
            'date-from'         => 'Du',
            'date-to'           => 'Au',
        ],

        'toolbar-actions' => [
            'export' => 'Exporter',
        ],

        'record-actions' => [
            'add-checkout' => 'Ajouter le départ',
        ],
    ],
];
