<?php

return [
    'form' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Détails de l\'Activité',

                'fields' => [
                    'activity-type' => 'Type d\'Activité',
                    'summary'       => 'Résumé',
                    'note'          => 'Note',
                ],
            ],

            'assignment' => [
                'title' => 'Attribution',

                'fields' => [
                    'assignment' => 'Attribution',
                    'assignee'   => 'Assigné à',
                ],
            ],

            'delay-information' => [
                'title' => 'Informations sur le Délai',

                'fields' => [
                    'delay-count'            => 'Nombre de Délais',
                    'delay-unit'             => 'Unité de Délai',
                    'delay-from'             => 'Délai à Partir de',
                    'delay-from-helper-text' => 'Source du calcul du délai',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'activity-type' => 'Type d\'Activité',
            'summary'       => 'Résumé',
            'assignment'    => 'Attribution',
            'assigned-to'   => 'Assigné à',
            'interval'      => 'Intervalle',
            'delay-unit'    => 'Unité de Délai',
            'delay-from'    => 'Délai à Partir de',
            'created-by'    => 'Créé par',
            'created-at'    => 'Créé le',
            'updated-at'    => 'Mis à Jour le',
        ],

        'groups' => [
            'activity-type' => 'Type d\'Activité',
            'assignment'    => 'Attribution',
            'assigned-to'   => 'Assigné à',
            'interval'      => 'Intervalle',
            'delay-unit'    => 'Unité de Délai',
            'delay-from'    => 'Délai à Partir de',
            'created-by'    => 'Créé par',
            'created-at'    => 'Créé le',
            'updated-at'    => 'Mis à Jour le',
        ],

        'filters' => [
            'activity-type'   => 'Type d\'Activité',
            'activity-status' => 'Statut de l\'Activité',
            'has-delay'       => 'Avec Délai',
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Modèle de plan d\'activité créé',
                    'body'  => 'Le modèle de plan d\'activité a été créé avec succès.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Modèle d\'activité mis à jour',
                    'body'  => 'Le modèle d\'activité a été mis à jour avec succès.',
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
];
