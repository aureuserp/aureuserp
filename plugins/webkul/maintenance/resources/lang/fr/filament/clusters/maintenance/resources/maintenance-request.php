<?php

return [
    'navigation' => [
        'group' => 'Maintenance',
        'title' => 'Demandes de maintenance',
    ],

    'form' => [
        'sections' => [
            'request' => [
                'title'  => 'Demande',
                'fields' => [
                    'name'                      => 'Demande',
                    'name-placeholder'          => 'ex. Écran ne fonctionne pas',
                    'equipment'                 => 'Équipement',
                    'category'                  => 'Catégorie',
                    'requested-at'              => 'Date de la demande',
                    'requested-at-hint-tooltip' => 'La date à laquelle la demande de maintenance a été signalée.',
                    'maintenance-type'          => 'Type de maintenance',
                    'recurrent'                 => 'Récurrente',
                    'repeat-every'              => 'Répéter tous les',
                    'maintenance-type-options'  => [
                        'corrective' => 'Corrective',
                        'preventive' => 'Préventive',
                    ],
                ],
                'tabs' => [
                    'notes' => [
                        'title'  => 'Notes',
                        'fields' => [
                            'description'             => 'Notes internes',
                            'description-placeholder' => 'Notes internes',
                        ],
                    ],
                    'instructions' => [
                        'title'  => 'Instructions',
                        'fields' => [
                            'instruction-type'         => 'Type d\'instruction',
                            'instruction-type-options' => [
                                'pdf'          => 'PDF',
                                'google-slide' => 'Google Slide',
                                'text'         => 'Texte',
                            ],
                            'instruction-pdf'              => 'PDF',
                            'instruction-google-slide'     => 'Google Slide',
                            'instruction-text'             => 'Description',
                            'instruction-text-placeholder' => 'Description',
                        ],
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'Paramètres',
                'fields' => [
                    'team'                      => 'Équipe',
                    'responsible'               => 'Responsable',
                    'scheduled-at'              => 'Date planifiée',
                    'scheduled-at-hint-tooltip' => 'La date et l\'heure auxquelles ces travaux de maintenance sont prévus pour commencer.',
                    'duration'                  => 'Durée',
                    'duration-hint-tooltip'     => 'Durée de maintenance prévue.',
                    'duration-suffix'           => 'heures',
                    'priority'                  => 'Priorité',
                    'company'                   => 'Société',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Sujets',
            'creator'    => 'Créé par l\'utilisateur',
            'technician' => 'Technicien',
            'category'   => 'Catégorie',
            'stage'      => 'Étape',
            'company'    => 'Société',
        ],

        'groups' => [
            'stage'       => 'Étape',
            'assigned-to' => 'Assigné à',
            'category'    => 'Catégorie',
            'created-by'  => 'Créé par',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Demande de maintenance restaurée',
                    'body'  => 'La demande de maintenance a été restaurée avec succès.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Demande de maintenance archivée',
                    'body'  => 'La demande de maintenance a été archivée avec succès.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Demande de maintenance supprimée',
                        'body'  => 'La demande de maintenance a été supprimée définitivement.',
                    ],
                    'error' => [
                        'title' => 'La demande de maintenance n\'a pas pu être supprimée',
                        'body'  => 'Cette demande de maintenance est référencée par un autre enregistrement.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Demandes de maintenance restaurées',
                    'body'  => 'Les demandes de maintenance sélectionnées ont été restaurées avec succès.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Demandes de maintenance archivées',
                    'body'  => 'Les demandes de maintenance sélectionnées ont été archivées avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'request' => [
                'title'   => 'Demande',
                'entries' => [
                    'name'                     => 'Demande',
                    'equipment'                => 'Équipement',
                    'category'                 => 'Catégorie',
                    'requested-at'             => 'Date de la demande',
                    'maintenance-type'         => 'Type de maintenance',
                    'instruction-type'         => 'Type d\'instruction',
                    'instruction-pdf'          => 'PDF',
                    'instruction-google-slide' => 'Google Slide',
                    'description'              => 'Notes internes',
                    'instruction-text'         => 'Description',
                ],
            ],

            'settings' => [
                'title'   => 'Paramètres',
                'entries' => [
                    'team'            => 'Équipe',
                    'responsible'     => 'Responsable',
                    'scheduled-at'    => 'Date planifiée',
                    'duration'        => 'Durée',
                    'duration-suffix' => 'heures',
                    'priority'        => 'Priorité',
                    'company'         => 'Société',
                ],
            ],
        ],
    ],
];
