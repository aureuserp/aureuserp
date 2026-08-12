<?php

return [
    'navigation' => [
        'title' => 'Projets',
        'group' => 'Projet',
    ],

    'global-search' => [
        'project-manager' => 'Chef de Projet',
        'customer'        => 'Client',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'name'             => 'Nom',
                    'name-placeholder' => 'Nom du Projet...',
                    'description'      => 'Description',
                ],
            ],

            'additional' => [
                'title' => 'Informations Supplémentaires',

                'fields' => [
                    'project-manager'             => 'Chef de Projet',
                    'customer'                    => 'Client',
                    'start-date'                  => 'Date de Début',
                    'end-date'                    => 'Date de Fin',
                    'allocated-hours'             => 'Heures Allouées',
                    'allocated-hours-helper-text' => 'En heures (Ex. 1,5 heures signifie 1 heure 30 minutes)',
                    'tags'                        => 'Étiquettes',
                    'company'                     => 'Entreprise',
                ],
            ],

            'settings' => [
                'title' => 'Paramètres',

                'fields' => [
                    'visibility'                   => 'Visibilité',
                    'visibility-hint-tooltip'      => 'Permettre aux employés d\'accéder à votre projet ou à vos tâches en les ajoutant comme abonnés. Ils accéderont automatiquement à toutes les tâches qui leur sont assignées.',
                    'private-description'          => 'Utilisateurs internes invités uniquement.',
                    'internal-description'         => 'Tous les utilisateurs internes peuvent voir.',
                    'public-description'           => 'Utilisateurs de portail invités et tous les utilisateurs internes.',
                    'time-management'              => 'Gestion du Temps',
                    'allow-timesheets'             => 'Autoriser les Feuilles de Temps',
                    'allow-timesheets-helper-text' => 'Enregistrez le temps sur les tâches et suivez la progression',
                    'task-management'              => 'Gestion des Tâches',
                    'allow-milestones'             => 'Autoriser les Jalons',
                    'allow-milestones-helper-text' => 'Suivre les jalons clés essentiels à la réussite.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Nom',
            'customer'        => 'Client',
            'start-date'      => 'Date de Début',
            'end-date'        => 'Date de Fin',
            'planned-date'    => 'Date Prévue',
            'remaining-hours' => 'Heures Restantes',
            'project-manager' => 'Chef de Projet',
        ],

        'groups' => [
            'stage'           => 'Étape',
            'project-manager' => 'Chef de Projet',
            'customer'        => 'Client',
            'created-at'      => 'Créé le',
        ],

        'filters' => [
            'name'             => 'Nom',
            'visibility'       => 'Visibilité',
            'start-date'       => 'Date de Début',
            'end-date'         => 'Date de Fin',
            'allow-timesheets' => 'Autoriser les Feuilles de Temps',
            'allow-milestones' => 'Autoriser les Jalons',
            'allocated-hours'  => 'Heures Allouées',
            'created-at'       => 'Créé le',
            'updated-at'       => 'Mis à Jour le',
            'stage'            => 'Étape',
            'customer'         => 'Client',
            'project-manager'  => 'Chef de Projet',
            'company'          => 'Entreprise',
            'creator'          => 'Créateur',
            'tags'             => 'Étiquettes',
        ],

        'actions' => [
            'tasks'      => ':count Tâches',
            'milestones' => ':completed jalons complétés sur :all',

            'restore' => [
                'notification' => [
                    'title' => 'Projet restauré',
                    'body'  => 'Le projet a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Projet supprimé',
                    'body'  => 'Le projet a été supprimé avec succès.',
                ],
            ],

            'force-delete' => [

                'notification' => [

                    'success' => [
                        'title' => 'Projet supprimé définitivement',
                        'body'  => 'Le projet a été supprimé définitivement avec succès.',
                    ],

                    'error' => [
                        'title' => 'Le projet ne peut pas être supprimé définitivement',
                        'body'  => 'Le projet est associé à d\'autres enregistrements.',
                    ],

                ],
            ],

        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'entries' => [
                    'name'             => 'Nom',
                    'name-placeholder' => 'Nom du Projet...',
                    'description'      => 'Description',
                ],
            ],

            'additional' => [
                'title' => 'Informations Supplémentaires',

                'entries' => [
                    'project-manager'        => 'Chef de Projet',
                    'customer'               => 'Client',
                    'project-timeline'       => 'Chronologie du Projet',
                    'allocated-hours'        => 'Heures Allouées',
                    'allocated-hours-suffix' => ' Heures',
                    'remaining-hours'        => 'Heures Restantes',
                    'remaining-hours-suffix' => ' Heures',
                    'current-stage'          => 'Étape Actuelle',
                    'tags'                   => 'Étiquettes',
                ],
            ],

            'statistics' => [
                'title' => 'Statistiques',

                'entries' => [
                    'total-tasks'         => 'Total des Tâches',
                    'milestones-progress' => 'Progression des Jalons',
                ],
            ],

            'record-information' => [
                'title' => 'Informations sur l\'Enregistrement',

                'entries' => [
                    'created-at'   => 'Créé le',
                    'created-by'   => 'Créé par',
                    'last-updated' => 'Dernière Mise à Jour',
                ],
            ],

            'settings' => [
                'title' => 'Paramètres du Projet',

                'entries' => [
                    'visibility'         => 'Visibilité',
                    'timesheets-enabled' => 'Feuilles de Temps Activées',
                    'milestones-enabled' => 'Jalons Activés',
                ],
            ],
        ],
    ],
];
