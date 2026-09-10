<?php

return [
    'navigation' => [
        'title' => 'Postes de travail',
        'group' => 'Configuration',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',
                'fields' => [
                    'name'                     => 'Nom',
                    'name-placeholder'         => 'ex. Ligne d\'assemblage 1',
                    'code'                     => 'Code',
                    'code-placeholder'         => 'ex. AL1',
                    'working-state'            => 'État de fonctionnement',
                    'color'                    => 'Couleur',
                    'tags'                     => 'Étiquette',
                    'alternative-work-centers' => 'Postes de travail alternatifs',
                    'company'                  => 'Société',
                    'calendar'                 => 'Heures de travail',
                ],
            ],

            'information' => [
                'title'     => 'Informations générales',
                'fieldsets' => [
                    'production-information' => 'Informations de production',
                    'costing-information'    => 'Informations de coût',
                ],
                'fields' => [
                    'default-capacity' => 'Capacité par défaut',
                    'time-efficiency'  => 'Efficacité du temps',
                    'oee-target'       => 'Objectif TRS',
                    'costs-per-hour'   => 'Coût par heure',
                    'cost-suffix'      => 'par heure',
                    'setup-time'       => 'Temps de préparation',
                    'cleanup-time'     => 'Temps de nettoyage',
                    'time-suffix'      => 'minutes',
                ],
            ],

            'description' => [
                'title'  => 'Description',
                'fields' => [
                    'note'             => 'Description',
                    'note-placeholder' => 'Description du poste de travail...',
                ],
            ],

            'specific-capacity' => [
                'title'  => 'Capacité spécifique',
                'fields' => [
                    'records' => 'Capacité spécifique',
                ],
                'columns' => [
                    'product'      => 'Produit',
                    'product-uom'  => 'UDM',
                    'capacity'     => 'Capacité',
                    'setup-time'   => 'Temps de préparation',
                    'cleanup-time' => 'Temps de nettoyage',
                ],
                'actions' => [
                    'add' => 'Ajouter une ligne',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Nom',
            'code'             => 'Code',
            'company'          => 'Société',
            'calendar'         => 'Heures de travail',
            'working-state'    => 'État de fonctionnement',
            'default-capacity' => 'Capacité',
            'time-efficiency'  => 'Efficacité',
            'costs-per-hour'   => 'Coût par heure',
            'deleted-at'       => 'Supprimé le',
            'created-at'       => 'Créé le',
            'updated-at'       => 'Mis à jour le',
        ],

        'groups' => [
            'company' => 'Société',
        ],

        'filters' => [
            'company'       => 'Société',
            'working-state' => 'État de fonctionnement',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Poste de travail restauré',
                    'body'  => 'Le poste de travail a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Poste de travail archivé',
                    'body'  => 'Le poste de travail a été archivé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Poste de travail supprimé',
                        'body'  => 'Le poste de travail a été définitivement supprimé.',
                    ],

                    'error' => [
                        'title' => 'Impossible de supprimer le poste de travail',
                        'body'  => 'Le poste de travail ne peut pas être supprimé car il est actuellement utilisé.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Postes de travail restaurés',
                    'body'  => 'Les postes de travail sélectionnés ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Postes de travail archivés',
                    'body'  => 'Les postes de travail sélectionnés ont été archivés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Postes de travail supprimés',
                        'body'  => 'Les postes de travail sélectionnés ont été définitivement supprimés.',
                    ],

                    'error' => [
                        'title' => 'Impossible de supprimer les postes de travail',
                        'body'  => 'Un ou plusieurs postes de travail sélectionnés sont actuellement utilisés.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informations générales',

                'entries' => [
                    'name'                     => 'Nom du poste de travail',
                    'code'                     => 'Code',
                    'working-state'            => 'État de fonctionnement',
                    'tags'                     => 'Étiquette',
                    'alternative-work-centers' => 'Postes de travail alternatifs',
                    'company'                  => 'Société',
                    'calendar'                 => 'Heures de travail',
                ],
            ],

            'information' => [
                'title'     => 'Informations générales',
                'fieldsets' => [
                    'production-information' => 'Informations de production',
                    'costing-information'    => 'Informations de coût',
                ],

                'entries' => [
                    'default-capacity' => 'Capacité par défaut',
                    'time-efficiency'  => 'Efficacité du temps',
                    'oee-target'       => 'Objectif TRS',
                    'costs-per-hour'   => 'Coût par heure',
                    'cost-suffix'      => 'par poste de travail',
                    'setup-time'       => 'Temps de préparation',
                    'cleanup-time'     => 'Temps de nettoyage',
                    'time-suffix'      => 'minutes',
                ],
            ],

            'description' => [
                'title'   => 'Description',
                'entries' => [
                    'note' => 'Description',
                ],
            ],

            'specific-capacity' => [
                'title'   => 'Capacités spécifiques',
                'columns' => [
                    'product'      => 'Produit',
                    'product-uom'  => 'UDM',
                    'capacity'     => 'Capacité',
                    'setup-time'   => 'Temps de préparation',
                    'cleanup-time' => 'Temps de nettoyage',
                ],
            ],

            'record-information' => [
                'title' => 'Informations sur l\'enregistrement',

                'entries' => [
                    'created-by'   => 'Créé par',
                    'created-at'   => 'Créé le',
                    'last-updated' => 'Dernière mise à jour',
                ],
            ],
        ],
    ],
];
