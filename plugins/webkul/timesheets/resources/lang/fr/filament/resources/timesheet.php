<?php

return [
    'title' => 'Feuilles de temps',

    'navigation' => [
        'title' => 'Feuilles de temps',
    ],

    'global-search' => [
        'project' => 'Projet',
        'task'    => 'Tâche',
        'date'    => 'Date',
    ],

    'form' => [
        'date'                   => 'Date',
        'employee'               => 'Employé',
        'project'                => 'Projet',
        'task'                   => 'Tâche',
        'description'            => 'Description',
        'time-spent'             => 'Temps passé',
        'time-spent-helper-text' => 'Temps passé en heures (Ex. 1,5 heure signifie 1 heure 30 minutes)',
    ],

    'table' => [
        'columns' => [
            'date'        => 'Date',
            'employee'    => 'Employé',
            'project'     => 'Projet',
            'task'        => 'Tâche',
            'description' => 'Description',
            'time-spent'  => 'Temps passé',
            'created-at'  => 'Créé le',
            'updated-at'  => 'Mis à jour le',
        ],

        'groups' => [
            'date'       => 'Date',
            'employee'   => 'Employé',
            'project'    => 'Projet',
            'task'       => 'Tâche',
            'creator'    => 'Créateur',
        ],

        'filters' => [
            'date-from'  => 'Date de début',
            'date-until' => "Date de fin",
            'employee'   => 'Employé',
            'project'    => 'Projet',
            'task'       => 'Tâche',
            'creator'    => 'Créateur',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Feuille de temps mise à jour',
                    'body'  => 'La feuille de temps a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Feuille de temps supprimée',
                    'body'  => 'La feuille de temps a été supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Feuilles de temps supprimées',
                    'body'  => 'Les feuilles de temps ont été supprimées avec succès.',
                ],
            ],
        ],
    ],
];
