<?php

return [
    'title' => 'Tâches',

    'navigation' => [
        'title' => 'Tâches',
        'group' => 'Projet',
    ],

    'global-search' => [
        'project'   => 'Projet',
        'customer'  => 'Client',
        'milestone' => 'Jalon',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'title'             => 'Titre',
                    'title-placeholder' => 'Titre de la Tâche...',
                    'tags'              => 'Étiquettes',
                    'name'              => 'Nom',
                    'color'             => 'Couleur',
                    'description'       => 'Description',
                    'project'           => 'Projet',
                    'status'            => 'Statut',
                    'start_date'        => 'Date de Début',
                    'end_date'          => 'Date de Fin',
                ],
            ],

            'additional' => [
                'title' => 'Informations Supplémentaires',
            ],

            'settings' => [
                'title' => 'Paramètres',

                'fields' => [
                    'project'                     => 'Projet',
                    'milestone'                   => 'Jalon',
                    'milestone-hint-text'         => 'Livrez automatiquement vos services en atteignant un jalon en le liant à un élément de commande client.',
                    'name'                        => 'Nom',
                    'deadline'                    => 'Échéance',
                    'is-completed'                => 'Est Complétée',
                    'customer'                    => 'Client',
                    'assignees'                   => 'Assignés',
                    'allocated-hours'             => 'Heures Allouées',
                    'allocated-hours-helper-text' => 'En heures (Ex. 1,5 heures signifie 1 heure 30 minutes)',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                  => 'ID',
            'priority'            => 'Priorité',
            'state'               => 'État',
            'new-state'           => 'Nouvel État',
            'update-state'        => 'Mettre à Jour l\'État',
            'title'               => 'Titre',
            'project'             => 'Projet',
            'project-placeholder' => 'Tâche Privée',
            'milestone'           => 'Jalon',
            'customer'            => 'Client',
            'assignees'           => 'Assignés',
            'allocated-time'      => 'Temps Alloué',
            'time-spent'          => 'Temps Passé',
            'time-remaining'      => 'Temps Restant',
            'progress'            => 'Progression',
            'deadline'            => 'Échéance',
            'tags'                => 'Étiquettes',
            'stage'               => 'Étape',
        ],

        'groups' => [
            'state'      => 'État',
            'project'    => 'Projet',
            'milestone'  => 'Jalon',
            'customer'   => 'Client',
            'deadline'   => 'Échéance',
            'stage'      => 'Étape',
            'created-at' => 'Créé le',
        ],

        'filters' => [
            'title'             => 'Titre',
            'priority'          => 'Priorité',
            'low'               => 'Basse',
            'high'              => 'Haute',
            'state'             => 'État',
            'tags'              => 'Étiquettes',
            'allocated-hours'   => 'Heures Allouées',
            'total-hours-spent' => 'Total des Heures Passées',
            'remaining-hours'   => 'Heures Restantes',
            'overtime'          => 'Heures Supplémentaires',
            'progress'          => 'Progression',
            'deadline'          => 'Échéance',
            'created-at'        => 'Créé le',
            'updated-at'        => 'Mis à Jour le',
            'assignees'         => 'Assignés',
            'customer'          => 'Client',
            'project'           => 'Projet',
            'stage'             => 'Étape',
            'milestone'         => 'Jalon',
            'company'           => 'Entreprise',
            'creator'           => 'Créateur',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tâche restaurée',
                    'body'  => 'La tâche a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tâche supprimée',
                    'body'  => 'La tâche a été supprimée avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tâche supprimée définitivement',
                    'body'  => 'La tâche a été supprimée définitivement avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tâches restaurées',
                    'body'  => 'Les tâches ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tâches supprimées',
                    'body'  => 'Les tâches ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tâches supprimées définitivement',
                    'body'  => 'Les tâches ont été supprimées définitivement avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'entries' => [
                    'title'       => 'Titre',
                    'state'       => 'État',
                    'tags'        => 'Étiquettes',
                    'priority'    => 'Priorité',
                    'description' => 'Description',
                ],
            ],

            'project-information' => [
                'title' => 'Informations du Projet',

                'entries' => [
                    'project'   => 'Projet',
                    'milestone' => 'Jalon',
                    'customer'  => 'Client',
                    'assignees' => 'Assignés',
                    'deadline'  => 'Échéance',
                    'stage'     => 'Étape',
                ],
            ],

            'time-tracking' => [
                'title' => 'Suivi du Temps',

                'entries' => [
                    'allocated-time'        => 'Temps Alloué',
                    'time-spent'            => 'Temps Passé',
                    'time-spent-suffix'     => ' Heures',
                    'time-remaining'        => 'Temps Restant',
                    'time-remaining-suffix' => ' Heures',
                    'progress'              => 'Progression',
                ],
            ],

            'additional-information' => [
                'title' => 'Informations Supplémentaires',
            ],

            'record-information' => [
                'title' => 'Informations sur l\'Enregistrement',

                'entries' => [
                    'created-at'   => 'Créé le',
                    'created-by'   => 'Créé par',
                    'last-updated' => 'Dernière Mise à Jour',
                ],
            ],

            'statistics' => [
                'title' => 'Statistiques',

                'entries' => [
                    'sub-tasks'         => 'Sous-Tâches',
                    'timesheet-entries' => 'Entrées de Feuille de Temps',
                ],
            ],
        ],
    ],
];
