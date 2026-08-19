<?php

return [
    'form' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Détails de l\'activité',

                'fields' => [
                    'activity-type' => 'Type d\'activité',
                    'summary'       => 'Résumé',
                    'note'          => 'Note',
                ],
            ],

            'assignment' => [
                'title' => 'Affectation',

                'fields' => [
                    'assignment' => 'Affectation',
                    'assignee'   => 'Assigné à',
                ],
            ],

            'delay-information' => [
                'title' => 'Informations sur le délai',

                'fields' => [
                    'delay-count'            => 'Nombre de jours de délai',
                    'delay-unit'             => 'Unité de délai',
                    'delay-from'             => 'Délai à partir de',
                    'delay-from-helper-text' => 'Source du calcul du délai',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'activity-type' => 'Type d\'activité',
            'summary'       => 'Résumé',
            'assignment'    => 'Affectation',
            'assigned-to'   => 'Assigné à',
            'interval'      => 'Intervalle',
            'delay-unit'    => 'Unité de délai',
            'delay-from'    => 'Délai à partir de',
            'created-by'    => 'Créé par',
            'created-at'    => 'Créé le',
            'updated-at'    => 'Mis à jour le',
        ],

        'groups' => [
            'activity-type' => 'Type d\'activité',
            'assignment'    => 'Affectation',
            'assigned-to'   => 'Assigné à',
            'interval'      => 'Intervalle',
            'delay-unit'    => 'Unité de délai',
            'delay-from'    => 'Délai à partir de',
            'created-by'    => 'Créé par',
            'created-at'    => 'Créé le',
            'updated-at'    => 'Mis à jour le',
        ],

        'filters' => [
            'activity-type'   => 'Type d\'activité',
            'activity-status' => 'Statut de l\'activité',
            'has-delay'       => 'A un délai',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Modèle d\'activité mis à jour',
                    'body'  => 'Le modèle d\'activité a été mis à jour avec succès.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Modèle d\'activité créé',
                    'body'  => 'Le modèle d\'activité a été créé avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Modèle d\'activité supprimé',
                    'body'  => 'Le modèle d\'activité a été supprimé avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Modèles d\'activité supprimés',
                    'body'  => 'Les modèles d\'activité ont été supprimés avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Détails de l\'activité',

                'entries' => [
                    'activity-type' => 'Type d\'activité',
                    'summary'       => 'Résumé',
                ],
            ],

            'assignment' => [
                'title' => 'Affectation',

                'entries' => [
                    'assignment' => 'Affectation',
                    'assignee'   => 'Assigné à',
                ],
            ],

            'delay-information' => [
                'title' => 'Informations sur le délai',

                'entries' => [
                    'delay-count'            => 'Nombre de jours de délai',
                    'delay-unit'             => 'Unité de délai',
                    'delay-from'             => 'Délai à partir de',
                    'delay-from-helper-text' => 'Source du calcul du délai',
                ],
            ],
        ],

        'note' => 'Note',
    ],
];
