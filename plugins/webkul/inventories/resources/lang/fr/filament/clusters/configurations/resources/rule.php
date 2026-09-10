<?php

return [
    'navigation' => [
        'title' => 'Règles',
        'group' => 'Gestion d\'entrepôt',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'fields' => [
                    'name'                        => 'Nom',
                    'action'                      => 'Action',
                    'operation-type'              => 'Type d\'opération',
                    'source-location'             => 'Emplacement source',
                    'destination-location'        => 'Emplacement de destination',
                    'supply-method'               => 'Méthode d\'approvisionnement',
                    'supply-method-hint-tooltip'  => 'Prélever sur le stock : les produits sont prélevés directement du stock disponible à l\'emplacement source.<br/>Déclencher une autre règle : le système ignore le stock disponible et recherche une règle de stock pour réapprovisionner l\'emplacement source.<br/>Prélever sur le stock, si indisponible, déclencher une autre règle : les produits sont d\'abord prélevés sur le stock disponible. S\'il n\'y en a pas, le système applique une règle de stock pour amener des produits à l\'emplacement source.',
                    'automatic-move'              => 'Mouvement automatique',
                    'automatic-move-hint-tooltip' => 'Opération manuelle : crée un mouvement de stock distinct après le mouvement actuel.<br/>Automatique sans étape ajoutée : remplace directement l\'emplacement dans le mouvement d\'origine sans ajouter d\'étape supplémentaire.',

                    'action-information' => [
                        'pull'        => 'Lorsque des produits sont requis dans <b>:sourceLocation</b>, :operation est généré depuis <b>:destinationLocation</b> pour répondre à la demande.',
                        'push'        => 'Lorsque des produits arrivent à <b>:sourceLocation</b>,</br><b>:operation</b> est généré pour les transférer vers <b>:destinationLocation</b>.',
                        'buy'         => 'Lorsque des produits sont nécessaires dans <b>:destinationLocation</b>, une demande de prix est créée pour répondre au besoin.',
                        'manufacture' => 'Lorsque des produits sont nécessaires dans <b>:destinationLocation</b>, un ordre de fabrication est créé pour répondre au besoin.',
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'Paramètres',

                'fields' => [
                    'partner-address'              => 'Adresse du partenaire',
                    'partner-address-hint-tooltip' => 'Adresse où les marchandises doivent être livrées. Facultatif.',
                    'lead-time'                    => 'Délai (jours)',
                    'lead-time-hint-tooltip'       => 'La date de transfert prévue sera calculée à l\'aide de ce délai.',
                ],

                'fieldsets' => [
                    'applicability' => [
                        'title'  => 'Applicabilité',

                        'fields' => [
                            'route'   => 'Route',
                            'company' => 'Société',
                        ],
                    ],

                    'propagation' => [
                        'title'  => 'Propagation',

                        'fields' => [
                            'propagation-procurement-group'              => 'Propagation du groupe d\'approvisionnement',
                            'propagation-procurement-group-hint-tooltip' => 'Si sélectionné, l\'annulation du mouvement créé par cette règle annulera également le mouvement suivant.',
                            'cancel-next-move'                           => 'Annuler le mouvement suivant',
                            'warehouse-to-propagate'                     => 'Entrepôt à propager',
                            'warehouse-to-propagate-hint-tooltip'        => 'L\'entrepôt affecté au mouvement ou à l\'approvisionnement créé, qui peut différer de l\'entrepôt auquel cette règle s\'applique (par exemple, pour les règles de réapprovisionnement depuis un autre entrepôt).',
                        ],
                    ],
                ],

            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Nom',
            'action'               => 'Action',
            'source-location'      => 'Emplacement source',
            'destination-location' => 'Emplacement de destination',
            'route'                => 'Route',
            'deleted-at'           => 'Supprimé le',
            'created-at'           => 'Créé le',
            'updated-at'           => 'Mis à jour le',
        ],

        'groups' => [
            'action'               => 'Action',
            'source-location'      => 'Emplacement source',
            'destination-location' => 'Emplacement de destination',
            'route'                => 'Route',
            'created-at'           => 'Créé le',
            'updated-at'           => 'Mis à jour le',
        ],

        'filters' => [
            'action'               => 'Action',
            'source-location'      => 'Emplacement source',
            'destination-location' => 'Emplacement de destination',
            'route'                => 'Route',
            'company'              => 'Société',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Règle mise à jour',
                    'body'  => 'La règle a été mise à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Règle restaurée',
                    'body'  => 'La règle a été restaurée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Règle supprimée',
                    'body'  => 'La règle a été supprimée avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Règle définitivement supprimée',
                        'body'  => 'La règle a été définitivement supprimée avec succès.',
                    ],

                    'error' => [
                        'title' => 'Impossible de supprimer la règle',
                        'body'  => 'La règle ne peut pas être supprimée car elle est actuellement utilisée.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Règles restaurées',
                    'body'  => 'Les règles ont été restaurées avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Règles supprimées',
                    'body'  => 'Les règles ont été supprimées avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Règles définitivement supprimées',
                        'body'  => 'Les règles ont été définitivement supprimées avec succès.',
                    ],

                    'error' => [
                        'title' => 'Impossible de supprimer les règles',
                        'body'  => 'Les règles ne peuvent pas être supprimées car elles sont actuellement utilisées.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Détails de la règle',

                'description' => [
                    'pull' => 'Lorsque des produits sont requis dans <b>:sourceLocation</b>, <b>:operation</b> est généré depuis <b>:destinationLocation</b> pour répondre à la demande.',
                    'push' => 'Lorsque des produits arrivent à <b>:sourceLocation</b>, <b>:operation</b> est généré pour les transférer vers <b>:destinationLocation</b>.',
                ],

                'entries' => [
                    'name'                 => 'Nom de la règle',
                    'action'               => 'Action',
                    'operation-type'       => 'Type d\'opération',
                    'source-location'      => 'Emplacement source',
                    'destination-location' => 'Emplacement de destination',
                    'route'                => 'Route',
                    'company'              => 'Société',
                    'partner-address'      => 'Adresse du partenaire',
                    'lead-time'            => 'Délai',
                    'action-information'   => 'Informations sur l\'action',
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
