<?php

return [
    'navigation' => [
        'title' => 'Opérations',
        'group' => 'Configuration',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',
                'fields' => [
                    'name'              => 'Opération',
                    'name-placeholder'  => 'ex. Découpe',
                    'bill-of-material'  => 'Nomenclature',
                    'work-center'       => 'Poste de travail',
                    'apply-on-variants' => 'Appliquer sur les variantes',
                    'company'           => 'Société',
                    'blocked-by'        => 'Bloqué par',
                ],
            ],
            'settings' => [
                'title'  => 'Paramètres',
                'fields' => [
                    'time-mode'                  => 'Calcul de la durée',
                    'time-mode-batch'            => 'Basé sur',
                    'time-mode-batch-prefix'     => 'les',
                    'time-mode-batch-suffix'     => 'derniers ordres de travail',
                    'manual-cycle-time'          => 'Durée par défaut',
                    'manual-cycle-time-suffix'   => 'minutes',
                ],
            ],
            'worksheet' => [
                'title'  => 'Fiche de travail',
                'fields' => [
                    'worksheet'                => 'Fiche de travail',
                    'pdf'                      => 'PDF',
                    'google-slide'             => 'Google Slide',
                    'google-slide-placeholder' => 'Lien Google Slide',
                    'description'              => 'Description',
                    'description-placeholder'  => 'Description de l\'opération...',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'              => 'Opération',
            'bill-of-material'  => 'Nomenclature',
            'work-center'       => 'Poste de travail',
            'time-mode'         => 'Calcul de la durée',
            'manual-cycle-time' => 'Durée par défaut',
            'worksheet-type'    => 'Fiche de travail',
            'deleted-at'        => 'Supprimé le',
            'created-at'        => 'Créé le',
            'updated-at'        => 'Mis à jour le',
        ],
        'filters' => [
            'work-center'    => 'Poste de travail',
            'time-mode'      => 'Calcul de la durée',
            'worksheet-type' => 'Fiche de travail',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Opération restaurée',
                    'body'  => 'L\'opération a été restaurée avec succès.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Opération archivée',
                    'body'  => 'L\'opération a été archivée avec succès.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Opération supprimée',
                        'body'  => 'L\'opération a été définitivement supprimée.',
                    ],
                    'error' => [
                        'title' => 'Impossible de supprimer l\'opération',
                        'body'  => 'L\'opération ne peut pas être supprimée car elle est actuellement utilisée.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Opérations restaurées',
                    'body'  => 'Les opérations sélectionnées ont été restaurées avec succès.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Opérations archivées',
                    'body'  => 'Les opérations sélectionnées ont été archivées avec succès.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Opérations supprimées',
                        'body'  => 'Les opérations sélectionnées ont été définitivement supprimées.',
                    ],
                    'error' => [
                        'title' => 'Impossible de supprimer les opérations',
                        'body'  => 'Une ou plusieurs opérations sélectionnées sont actuellement utilisées.',
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
                    'name'              => 'Opération',
                    'bill-of-material'  => 'Nomenclature',
                    'work-center'       => 'Poste de travail',
                    'apply-on-variants' => 'Appliquer sur les variantes',
                    'company'           => 'Société',
                ],
            ],
            'settings' => [
                'title'   => 'Paramètres',
                'entries' => [
                    'time-mode'                => 'Calcul de la durée',
                    'time-mode-batch'          => 'Basé sur',
                    'manual-cycle-time'        => 'Durée par défaut',
                    'manual-cycle-time-suffix' => 'minutes',
                ],
            ],
            'worksheet' => [
                'title'   => 'Fiche de travail',
                'entries' => [
                    'worksheet'    => 'Fiche de travail',
                    'pdf'          => 'PDF',
                    'google-slide' => 'Google Slide',
                    'description'  => 'Description',
                ],
            ],
            'record-information' => [
                'title'   => 'Informations sur l\'enregistrement',
                'entries' => [
                    'created-by'   => 'Créé par',
                    'created-at'   => 'Créé le',
                    'last-updated' => 'Dernière mise à jour',
                ],
            ],
        ],
    ],
];
