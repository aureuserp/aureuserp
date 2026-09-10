<?php

return [
    'title' => 'Départements',

    'navigation' => [
        'title' => 'Départements',
        'group' => 'Employés',
    ],

    'form' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Informations générales',

                'fields' => [
                    'name'                => "Type d'activité",
                    'name-tooltip'        => "Saisissez le nom officiel du type d'activité",
                    'action'              => 'Action',
                    'default-user'        => 'Utilisateur par défaut',
                    'summary'             => 'Résumé',
                    'note'                => 'Note',
                ],
            ],

            'delay-information' => [
                'title' => 'Informations sur le délai',

                'fields' => [
                    'delay-count'            => 'Nombre de délais',
                    'delay-unit'             => 'Unité de délai',
                    'delay-form'             => 'Formulaire de délai',
                    'delay-form-helper-text' => 'Source du calcul du délai',
                ],
            ],

            'advanced-information' => [
                'title' => 'Informations avancées',

                'fields' => [
                    'icon'                => 'Icône',
                    'decoration-type'     => 'Type de décoration',
                    'chaining-type'       => "Type d'enchaînement",
                    'suggest'             => 'Suggérer',
                    'trigger'             => 'Déclencher',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Statut et configuration',

                'fields' => [
                    'status'               => 'Statut',
                    'keep-done-activities' => 'Conserver les activités terminées',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => "Type d'activité",
            'summary'    => 'Résumé',
            'planned-in' => 'Planifié dans',
            'type'       => 'Type',
            'action'     => 'Action',
            'status'     => 'Statut',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'groups' => [
            'name'             => 'Nom',
            'action-category'  => "Catégorie d'action",
            'status'           => 'Statut',
            'delay-count'      => 'Nombre de délais',
            'delay-unit'       => 'Unité de délai',
            'delay-source'     => 'Source du délai',
            'associated-model' => 'Modèle associé',
            'chaining-type'    => "Type d'enchaînement",
            'decoration-type'  => 'Type de décoration',
            'default-user'     => 'Utilisateur par défaut',
            'creation-date'    => 'Date de création',
            'last-update'      => 'Dernière mise à jour',
        ],

        'filters' => [
            'action'    => 'Action',
            'status'    => 'Statut',
            'has-delay' => 'A un délai',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => "Type d'activité restauré",
                    'body'  => "Le type d'activité a été restauré avec succès.",
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => "Type d'activité supprimé",
                    'body'  => "Le type d'activité a été supprimé avec succès.",
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => "Type d'activité définitivement supprimé",
                        'body'  => "Le type d'activité a été définitivement supprimé avec succès.",
                    ],
                    'error' => [
                        'title' => "Le type d'activité n'a pas pu être supprimé",
                        'body'  => "Le type d'activité ne peut pas être supprimé car il est actuellement utilisé.",
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => "Types d'activité restaurés",
                    'body'  => "Les types d'activité ont été restaurés avec succès.",
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => "Types d'activité supprimés",
                    'body'  => "Les types d'activité ont été supprimés avec succès.",
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => "Types d'activité définitivement supprimés",
                    'body'  => "Les types d'activité ont été définitivement supprimés avec succès.",
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Informations générales',

                'entries' => [
                    'name'                => "Type d'activité",
                    'name-tooltip'        => "Saisissez le nom officiel du type d'activité",
                    'action'              => 'Action',
                    'default-user'        => 'Utilisateur par défaut',
                    'plugin'              => 'Plugin',
                    'summary'             => 'Résumé',
                    'note'                => 'Note',
                ],
            ],

            'delay-information' => [
                'title' => 'Informations sur le délai',

                'entries' => [
                    'delay-count'            => 'Nombre de délais',
                    'delay-unit'             => 'Unité de délai',
                    'delay-form'             => 'Formulaire de délai',
                    'delay-form-helper-text' => 'Source du calcul du délai',
                ],
            ],

            'advanced-information' => [
                'title' => 'Informations avancées',

                'entries' => [
                    'icon'                => 'Icône',
                    'decoration-type'     => 'Type de décoration',
                    'chaining-type'       => "Type d'enchaînement",
                    'suggest'             => 'Suggérer',
                    'trigger'             => 'Déclencher',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Statut et configuration',

                'entries' => [
                    'status'               => 'Statut',
                    'keep-done-activities' => 'Conserver les activités terminées',
                ],
            ],
        ],
    ],
];
