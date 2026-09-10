<?php

return [
    'setup' => [
        'title'               => 'Planifier une activité',
        'submit-action-title' => 'Planifier',

        'form' => [
            'fields' => [
                'activity-plan' => 'Plan d\'activité',
                'plan-date'     => 'Date du plan',
                'plan-summary'  => 'Résumé du plan',
                'activity-type' => 'Type d\'activité',
                'due-date'      => 'Date d\'échéance',
                'summary'       => 'Résumé',
                'assigned-to'   => 'Assigné à',
                'log-note'      => 'Note de journal',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Activité créée',
                    'body'  => 'L\'activité a été créée.',
                ],

                'warning'  => [
                    'title' => 'Aucun nouveau fichier',
                    'body'  => 'Tous les fichiers ont déjà été téléchargés.',
                ],

                'error' => [
                    'title' => 'Échec de la création de l\'activité',
                    'body'  => 'Échec de la création de l\'activité ',
                ],
            ],
        ],
    ],
];
