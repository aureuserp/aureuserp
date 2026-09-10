<?php

return [
    'title' => 'Variantes',

    'form' => [
        'date'                   => 'Date',
        'employee'               => 'Employé',
        'description'            => 'Description',
        'time-spent'             => 'Temps passé',
        'time-spent-helper-text' => 'Temps passé en heures (Ex. 1,5 heure signifie 1 heure 30 minutes)',
    ],

    'table' => [
        'columns' => [
            'date'                   => 'Date',
            'employee'               => 'Employé',
            'description'            => 'Description',
            'time-spent'             => 'Temps passé',
            'time-spent-on-subtasks' => 'Temps passé sur les sous-tâches',
            'total-time-spent'       => 'Temps total passé',
            'remaining-time'         => 'Temps restant',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Variante supprimée',
                    'body'  => 'La variante a été supprimée avec succès.',
                ],
            ],
        ],
    ],
];
