<?php

return [
    'title' => 'Devises',

    'navigation' => [
        'title' => 'Devises',
    ],

    'form' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Informations sur la devise',

                'fields' => [
                    'name'         => 'Nom de la devise',
                    'name-tooltip' => 'Saisissez le nom officiel de la devise',
                    'symbol'       => 'Symbole de la devise',
                    'full-name'    => 'Nom complet',
                    'iso-numeric'  => 'Code ISO numérique',
                ],
            ],

            'format-information' => [
                'title' => 'Configuration du format',

                'fields' => [
                    'decimal-places'        => 'Décimales',
                    'rounding'              => 'Précision d\'arrondi',
                    'rounding-helper-text'  => "Définissez la précision d'arrondi pour les calculs de devise",
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Statut et configuration',

                'fields' => [
                    'status' => 'Statut',
                ],
            ],

            'rates' => [
                'title'       => 'Taux de change',
                'description' => 'Gérez les taux de change historiques de cette devise par rapport à la devise de base (USD).',

                'fields' => [
                    'name'              => 'Date',
                    'unit-per-currency' => 'Unité par :currency',
                    'currency-per-unit' => ':currency par unité',
                ],

                'add-rate'   => 'Ajouter un taux',
                'item-label' => 'Taux',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Nom de la devise',
            'symbol'         => 'Symbole',
            'full-name'      => 'Nom complet',
            'iso-numeric'    => 'Code ISO',
            'decimal-places' => 'Décimales',
            'rounding'       => 'Arrondi',
            'status'         => 'Statut',
            'created-at'     => 'Créé le',
            'updated-at'     => 'Mis à jour le',
        ],

        'groups' => [
            'name'           => 'Nom',
            'status'         => 'Statut',
            'decimal-places' => 'Décimales',
            'creation-date'  => 'Date de création',
            'last-update'    => 'Dernière mise à jour',
        ],

        'filters' => [
            'status' => 'Statut',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title'   => 'Devise supprimée',
                    'body'    => 'La devise a été supprimée avec succès.',

                    'success' => [
                        'title' => 'Devise supprimée',
                        'body'  => 'La devise a été supprimée avec succès.',
                    ],

                    'error' => [
                        'title' => "La devise n'a pas pu être supprimée",
                        'body'  => 'La devise ne peut pas être supprimée car elle est actuellement utilisée.',
                    ],
                ],
            ],

            'deactivate' => [
                'notification' => [
                    'title' => 'La devise ne peut pas être désactivée',
                    'body'  => "Cette devise est utilisée par une ou plusieurs sociétés et ne peut pas être désactivée.",
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Devises supprimées',
                    'body'  => 'Les devises ont été supprimées avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Informations sur la devise',

                'entries' => [
                    'name'         => 'Nom de la devise',
                    'symbol'       => 'Symbole de la devise',
                    'full-name'    => 'Nom complet',
                    'iso-numeric'  => 'Code ISO numérique',
                ],
            ],

            'format-information' => [
                'title' => 'Configuration du format',

                'entries' => [
                    'decimal-places' => 'Décimales',
                    'rounding'       => 'Précision d\'arrondi',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Statut et configuration',

                'entries' => [
                    'status' => 'Statut',
                ],
            ],

            'rates' => [
                'title'       => 'Taux de change',

                'entries' => [
                    'name'              => 'Date',
                    'unit-per-currency' => 'Unité par :currency',
                    'currency-per-unit' => ':currency par unité',
                ],
            ],
        ],
    ],
];
