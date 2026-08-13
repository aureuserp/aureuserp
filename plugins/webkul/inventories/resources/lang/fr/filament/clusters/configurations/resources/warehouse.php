<?php

return [
    'navigation' => [
        'title' => 'Entrepôts',
        'group' => 'Gestion des entrepôts',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',
                'fields' => [
                    'name'                    => 'Nom',
                    'name-placeholder'        => 'ex. Entrepôt central',
                    'code'                    => 'Nom court',
                    'code-placeholder'        => 'ex. EC',
                    'code-hint-tooltip'       => "Le nom court sert d'identifiant pour l'entrepôt.",
                    'company'                 => 'Société',
                    'multi-warehouse-warning' => "La création d'un nouvel entrepôt activera automatiquement le paramètre Emplacements de stockage.",
                    'address'                 => 'Adresse',
                ],
            ],

            'settings' => [
                'title'  => 'Paramètres',

                'fields' => [
                    'shipment-management'              => 'Gestion des expéditions',
                    'incoming-shipments'                => 'Expéditions entrantes',
                    'incoming-shipments-hint-tooltip'   => 'Route entrante par défaut à suivre',
                    'outgoing-shipments'                => 'Expéditions sortantes',
                    'outgoing-shipments-hint-tooltip'   => 'Route sortante par défaut à suivre',
                    'manufacture'                       => 'Fabrication',
                    'manufacture-hint-tooltip'          => 'Route de fabrication par défaut à suivre',
                    'resupply-management'               => 'Gestion du réapprovisionnement',
                    'resupply-management-hint-tooltip'  => 'Des routes seront automatiquement générées pour réapprovisionner cet entrepôt à partir des entrepôts sélectionnés.',
                    'resupply-from'                     => 'Réapprovisionner depuis',
                ],
            ],

            'additional' => [
                'title'  => 'Informations complémentaires',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'code'       => 'Nom court',
            'company'    => 'Société',
            'address'    => 'Adresse',
            'deleted-at' => 'supprimé le',
            'created-at' => 'Créé le',
            'updated-at' => 'Mis à jour le',
        ],

        'groups' => [
            'address'       => 'Adresse',
            'company'       => 'Société',
            'created-at'    => 'Créé le',
            'updated-at'    => 'Mis à jour le',
        ],

        'filters' => [
            'company' => 'Société',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Entrepôt restauré',
                    'body'  => "L'entrepôt a été restauré avec succès.",
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Entrepôt supprimé',
                    'body'  => "L'entrepôt a été supprimé avec succès.",
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Entrepôt définitivement supprimé',
                        'body'  => "L'entrepôt a été définitivement supprimé avec succès.",
                    ],

                    'error' => [
                        'title' => "L'entrepôt n'a pas pu être supprimé",
                        'body'  => "L'entrepôt ne peut pas être supprimé car il est actuellement utilisé.",
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Entrepôts restaurés',
                    'body'  => 'Les entrepôts ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Entrepôts supprimés',
                    'body'  => 'Les entrepôts ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Entrepôts définitivement supprimés',
                        'body'  => 'Les entrepôts ont été définitivement supprimés avec succès.',
                    ],

                    'error' => [
                        'title' => "Les entrepôts n'ont pas pu être supprimés",
                        'body'  => 'Les entrepôts ne peuvent pas être supprimés car ils sont actuellement utilisés.',
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
                    'name'    => "Nom de l'entrepôt",
                    'code'    => "Code de l'entrepôt",
                    'company' => 'Société',
                    'address' => 'Adresse',
                ],
            ],

            'settings' => [
                'title' => 'Paramètres',

                'entries' => [
                    'shipment-management' => 'Gestion des expéditions',
                    'incoming-shipments'  => 'Expéditions entrantes',
                    'outgoing-shipments'  => 'Expéditions sortantes',
                    'resupply-management' => 'Gestion du réapprovisionnement',
                    'resupply-from'       => 'Réapprovisionner depuis',
                ],
            ],

            'record-information' => [
                'title' => "Informations sur l'enregistrement",

                'entries' => [
                    'created-by'   => 'Créé par',
                    'created-at'   => 'Créé le',
                    'last-updated' => 'Dernière mise à jour',
                ],
            ],
        ],
    ],
];
