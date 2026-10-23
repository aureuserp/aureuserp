<?php

return [
    'model-label'        => 'Activité',
    'plural-model-label' => 'Activités',

    'navigation' => [
        'title' => 'Mes activités',
    ],

    'views' => [
        'list'   => 'Liste',
        'kanban' => 'Kanban',
        'graph'  => 'Graphique',
    ],

    'table' => [
        'columns' => [
            'due-date'      => 'Date d\'échéance',
            'opportunity'   => 'Piste / Opportunité',
            'customer'      => 'Client',
            'activity-type' => 'Type d\'activité',
            'summary'       => 'Résumé',
            'assigned-to'   => 'Assigné à',
            'stage'         => 'Étape',
            'created-at'    => 'Créé le',
        ],

        'groups' => [
            'activity-type' => 'Type d\'activité',
            'assigned-to'   => 'Assigné à',
            'due-date'      => 'Date d\'échéance',
        ],

        'filters' => [
            'due-status'    => 'Échéance',
            'activity-type' => 'Type d\'activité',
            'assigned-to'   => 'Assigné à',
        ],

        'empty-state' => [
            'heading'     => 'Aucune activité',
            'description' => 'Utilisez Planifier une activité, ou l\'icône horloge d\'une opportunité, pour planifier votre prochaine étape.',
        ],
    ],

    'actions' => [
        'schedule' => [
            'label' => 'Planifier une activité',

            'modal' => [
                'heading' => 'Planifier une activité',
                'submit'  => 'Planifier',
            ],

            'form' => [
                'opportunity'   => 'Opportunité',
                'activity-type' => 'Type d\'activité',
                'due-date'      => 'Date d\'échéance',
                'summary'       => 'Résumé',
                'assigned-to'   => 'Assigné à',
                'note'          => 'Note',
            ],

            'notification' => [
                'success' => [
                    'title' => 'Activité planifiée',
                    'body'  => 'L\'activité a été planifiée sur « :opportunity ».',
                ],

                'forbidden' => [
                    'title' => 'Non autorisé',
                    'body'  => 'Vous n\'avez pas la permission de planifier des activités sur cette opportunité.',
                ],
            ],
        ],

        'open' => [
            'label' => 'Ouvrir l\'enregistrement',
        ],

        'done' => [
            'label' => 'Marquer comme terminée',

            'modal' => [
                'heading' => 'Marquer l\'activité comme terminée',
                'submit'  => 'Terminer',
            ],

            'form' => [
                'feedback' => 'Commentaire',
            ],

            'notification' => [
                'title' => 'Activité terminée',
                'body'  => 'L\'activité a été marquée comme terminée et consignée sur l\'opportunité.',
            ],
        ],

        'reschedule' => [
            'label' => 'Replanifier',

            'modal' => [
                'heading' => 'Replanifier l\'activité',
            ],

            'form' => [
                'due-date'    => 'Date d\'échéance',
                'summary'     => 'Résumé',
                'assigned-to' => 'Assigné à',
            ],

            'notification' => [
                'title' => 'Activité replanifiée',
                'body'  => 'L\'activité a été mise à jour.',
            ],
        ],

        'cancel' => [
            'label'      => 'Annuler l\'activité',
            'bulk-label' => 'Annuler les activités',

            'modal' => [
                'heading' => 'Annuler l\'activité',
            ],

            'notification' => [
                'title' => 'Activité annulée',
                'body'  => 'L\'activité a été supprimée.',
            ],
        ],
    ],
];
