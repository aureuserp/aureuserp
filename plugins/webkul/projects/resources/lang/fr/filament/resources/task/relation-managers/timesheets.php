<?php

return [
    'form' => [
        'date'                   => 'Date',
        'employee'               => 'Employé',
        'description'            => 'Description',
        'time-spent'             => 'Temps Passé',
        'time-spent-helper-text' => 'Temps passé en heures (Ex. 1,5 heures signifie 1 heure 30 minutes)',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Ajouter une Feuille de Temps',

                'notification' => [
                    'title' => 'Feuille de Temps créée',
                    'body'  => 'La feuille de temps a été créée avec succès.',
                ],
            ],
        ],

        'columns' => [
            'date'                   => 'Date',
            'employee'               => 'Employé',
            'description'            => 'Description',
            'time-spent'             => 'Temps Passé',
            'time-spent-on-subtasks' => 'Temps Passé sur les Sous-Tâches',
            'total-time-spent'       => 'Temps Total Passé',
            'remaining-time'         => 'Temps Restant',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Feuille de Temps mise à jour',
                    'body'  => 'La feuille de temps a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Feuille de Temps supprimée',
                    'body'  => 'La feuille de temps a été supprimée avec succès.',
                ],
            ],
        ],
    ],
];
