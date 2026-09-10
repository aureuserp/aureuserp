<?php

return [
    'navigation' => [
        'title' => 'Équipement',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Informations générales',
                'fields' => [
                    'name' => 'Nom',
                    'note' => 'Description',
                ],
            ],

            'settings' => [
                'title'  => 'Paramètres',
                'fields' => [
                    'category'   => 'Catégorie d\'équipement',
                    'team'       => 'Équipe de maintenance',
                    'company'    => 'Société',
                    'technician' => 'Technicien',
                    'owner'      => 'Propriétaire',
                    'location'   => 'Utilisé à l\'emplacement',
                ],
            ],

            'product-information' => [
                'title'  => 'Informations produit',
                'fields' => [
                    'partner'                     => 'Fournisseur',
                    'partner-ref'                 => 'Référence fournisseur',
                    'model'                       => 'Modèle',
                    'serial-no'                   => 'Numéro de série',
                    'effective-date'              => 'Date d\'effet',
                    'effective-date-hint-tooltip' => 'Utilisée comme point de départ pour le calcul du temps moyen entre pannes.',
                    'cost'                        => 'Coût',
                    'warranty-date'               => 'Date d\'expiration de la garantie',
                ],
            ],

            'maintenance' => [
                'title'  => 'Maintenance',
                'fields' => [
                    'expected-mtbf' => 'Temps moyen entre pannes prévu',
                ],
                'suffixes' => [
                    'days' => 'jours',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom de l\'équipement',
            'owner'      => 'Propriétaire',
            'serial-no'  => 'Numéro de série',
            'category'   => 'Catégorie d\'équipement',
            'technician' => 'Technicien',
            'company'    => 'Société',
            'created-at' => 'Créé le',
        ],

        'filters' => [
            'category'   => 'Catégorie d\'équipement',
            'team'       => 'Équipe de maintenance',
            'technician' => 'Technicien',
        ],

        'groups' => [
            'category'   => 'Catégorie d\'équipement',
            'owner'      => 'Propriétaire',
            'technician' => 'Technicien',
            'vendor'     => 'Fournisseur',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Équipement mis à jour',
                    'body'  => 'L\'équipement a été mis à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Équipement restauré',
                    'body'  => 'L\'équipement a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Équipement archivé',
                    'body'  => 'L\'équipement a été archivé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Équipement supprimé',
                        'body'  => 'L\'équipement a été supprimé définitivement.',
                    ],

                    'error' => [
                        'title' => 'L\'équipement n\'a pas pu être supprimé',
                        'body'  => 'Cet équipement est référencé par un autre enregistrement.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Équipement restauré',
                    'body'  => 'L\'équipement sélectionné a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Équipement archivé',
                    'body'  => 'L\'équipement sélectionné a été archivé avec succès.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Équipement créé',
                    'body'  => 'L\'équipement a été créé avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Informations générales',
                'entries' => [
                    'name' => 'Nom',
                    'note' => 'Description',
                ],
            ],

            'settings' => [
                'title'   => 'Paramètres',
                'entries' => [
                    'category'   => 'Catégorie d\'équipement',
                    'team'       => 'Équipe de maintenance',
                    'company'    => 'Société',
                    'technician' => 'Technicien',
                    'owner'      => 'Propriétaire',
                    'location'   => 'Utilisé à l\'emplacement',
                ],
            ],

            'product-information' => [
                'title'   => 'Informations produit',
                'entries' => [
                    'partner'        => 'Fournisseur',
                    'partner-ref'    => 'Référence fournisseur',
                    'model'          => 'Modèle',
                    'serial-no'      => 'Numéro de série',
                    'effective-date' => 'Date d\'effet',
                    'cost'           => 'Coût',
                    'warranty-date'  => 'Date d\'expiration de la garantie',
                ],
            ],

            'maintenance' => [
                'title'   => 'Maintenance',
                'entries' => [
                    'expected-mtbf'          => 'Temps moyen entre pannes prévu',
                    'maintenance-count'      => 'Nombre de maintenances',
                    'maintenance-open-count' => 'Nombre de maintenances ouvertes',
                    'assigned-at'            => 'Date d\'affectation',
                    'scraped-at'             => 'Date de mise au rebut',
                ],
                'suffixes' => [
                    'days' => 'jours',
                ],
            ],
        ],
    ],
];
