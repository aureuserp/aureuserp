<?php

return [
    'title'      => 'Type de congé',
    'navigation' => [
        'title' => 'Type de congé',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Informations générales',
                'fields' => [
                    'name'                => 'Titre',
                    'approval'            => 'Approbation',
                    'requires-allocation' => 'Nécessite une allocation',
                    'employee-requests'   => 'Demandes des employés',
                    'display-option'      => 'Option d\'affichage',
                ],
            ],
            'display-option' => [
                'title'  => 'Option d\'affichage',
                'fields' => [
                    'color' => 'Couleur',
                ],
            ],
            'configuration' => [
                'title' => 'Configuration',

                'fields' => [
                    'notified-time-off-officers'          => 'Responsables des congés notifiés',
                    'take-time-off-in'                    => 'Prendre le congé en',
                    'public-holiday-included'             => 'Jour férié inclus',
                    'allow-to-attach-supporting-document' => 'Autoriser la jointure d\'un justificatif',
                    'show-on-dashboard'                   => 'Afficher sur le tableau de bord',
                    'allow-negative-cap'                  => 'Autoriser le plafond négatif',
                    'kind-off-time'                       => 'Type de temps',
                    'max-negative-cap'                    => 'Plafond négatif maximum',
                    'kind-of-time'                        => 'Type de congé',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                      => 'Nom',
            'company-name'              => 'Société',
            'color'                     => 'Couleur',
            'notified-time-officers'    => 'Responsables des temps notifiés',
            'time-off-approval'         => 'Approbation du congé',
            'requires-allocation'       => 'Nécessite une allocation',
            'allocation-approval'       => 'Approbation de l\'allocation',
            'employee-request'          => 'Demande de l\'employé',
        ],

        'filters' => [
            'name'                => 'Nom',
            'company-name'        => 'Société',
            'time-off-approval'   => 'Approbation du congé',
            'requires-allocation' => 'Nécessite une allocation',
            'time-type'           => 'Type de temps',
            'request-unit'        => 'Unité de la demande',
            'created-by'          => 'Créé par',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Type de congé supprimé',
                    'body'  => 'Le type de congé a été supprimé avec succès.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Type de congé restauré',
                    'body'  => 'Le type de congé a été restauré avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Type de congé restauré',
                    'body'  => 'Le type de congé a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Type de congé supprimé',
                    'body'  => 'Le type de congé a été supprimé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Type de congé supprimé définitivement',
                        'body'  => 'Le type de congé a été supprimé définitivement avec succès.',
                    ],
                    'error' => [
                        'title' => 'Le type de congé n\'a pas pu être supprimé',
                        'body'  => 'Le type de congé ne peut pas être supprimé car il est actuellement utilisé.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Informations générales',
                'entries' => [
                    'name'                => 'Titre',
                    'approval'            => 'Approbation',
                    'requires-allocation' => 'Nécessite une allocation',
                    'employee-requests'   => 'Demandes des employés',
                    'display-option'      => 'Option d\'affichage',
                ],
            ],
            'display-option' => [
                'title'   => 'Option d\'affichage',
                'entries' => [
                    'color' => 'Couleur',
                ],
            ],
            'configuration' => [
                'title' => 'Configuration',

                'entries' => [
                    'notified-time-off-officers'          => 'Responsables des congés notifiés',
                    'take-time-off-in'                    => 'Prendre le congé en',
                    'public-holiday-included'             => 'Jour férié inclus',
                    'allow-to-attach-supporting-document' => 'Autoriser la jointure d\'un justificatif',
                    'show-on-dashboard'                   => 'Afficher sur le tableau de bord',
                    'kind-off-time'                       => 'Type de temps',
                    'max-negative-cap'                    => 'Plafond négatif maximum',
                    'kind-of-time'                        => 'Type de congé',
                ],
            ],
        ],
    ],
];
