<?php

return [
    'navigation' => [
        'title' => 'Emplacements',
        'group' => 'Gestion des entrepôts',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'fields' => [
                    'location'                     => 'Emplacement',
                    'location-placeholder'         => 'ex. Stock de réserve',
                    'parent-location'              => 'Emplacement parent',
                    'parent-location-hint-tooltip' => 'L\'emplacement principal qui englobe cet emplacement. Par exemple, la « Zone d\'expédition » fait partie de l\'emplacement parent « Porte 1 ».',
                    'external-notes'               => 'Notes externes',
                ],
            ],

            'settings' => [
                'title'  => 'Paramètres',

                'fields' => [
                    'location-type'                 => 'Type d\'emplacement',
                    'company'                       => 'Société',
                    'storage-category'              => 'Catégorie de stockage',
                    'is-scrap'                      => 'Est un emplacement de rebut ?',
                    'is-scrap-hint-tooltip'         => 'Cochez cette case pour désigner cet emplacement pour le stockage des marchandises mises au rebut ou endommagées.',
                    'is-dock'                       => 'Est un emplacement de quai ?',
                    'is-dock-hint-tooltip'          => 'Cochez cette case pour désigner cet emplacement pour le stockage des marchandises prêtes à être expédiées.',
                    'is-replenish'                  => 'Est un emplacement de réapprovisionnement ?',
                    'is-replenish-hint-tooltip'     => 'Activez cette fonction pour récupérer toutes les quantités nécessaires au réapprovisionnement de cet emplacement.',
                    'logistics'                     => 'Logistique',
                    'removal-strategy'              => 'Stratégie de retrait',
                    'removal-strategy-hint-tooltip' => 'Spécifie la méthode par défaut pour déterminer l\'étagère, le lot et l\'emplacement exacts à partir desquels prélever les produits. Cette méthode peut être imposée au niveau de la catégorie de produit, avec un repli sur les emplacements parents si elle n\'est pas définie ici.',
                    'cyclic-counting'               => 'Comptage cyclique',
                    'inventory-frequency'           => 'Fréquence d\'inventaire',
                    'last-inventory'                => 'Dernier inventaire',
                    'last-inventory-hint-tooltip'   => 'Date du dernier inventaire à cet emplacement.',
                    'next-expected'                 => 'Prochain prévu',
                    'next-expected-hint-tooltip'    => 'Date du prochain inventaire prévu selon le calendrier cyclique.',
                ],
            ],

            'additional' => [
                'title'  => 'Informations complémentaires',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'location'         => 'Emplacement',
            'type'             => 'Type',
            'storage-category' => 'Catégorie de stockage',
            'company'          => 'Société',
            'deleted-at'       => 'Supprimé le',
            'created-at'       => 'Créé le',
            'updated-at'       => 'Mis à jour le',
        ],

        'groups' => [
            'warehouse'       => 'Entrepôt',
            'type'            => 'Type',
            'created-at'      => 'Créé le',
            'updated-at'      => 'Mis à jour le',
        ],

        'filters' => [
            'location' => 'Emplacement',
            'type'     => 'Type',
            'company'  => 'Société',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Emplacement mis à jour',
                    'body'  => 'L\'emplacement a été mis à jour avec succès.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Emplacement restauré',
                    'body'  => 'L\'emplacement a été restauré avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Emplacement supprimé',
                    'body'  => 'L\'emplacement a été supprimé avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Emplacement définitivement supprimé',
                        'body'  => 'L\'emplacement a été définitivement supprimé avec succès.',
                    ],

                    'error' => [
                        'title' => 'L\'emplacement n\'a pas pu être supprimé',
                        'body'  => 'L\'emplacement ne peut pas être supprimé car il est actuellement utilisé.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Imprimer le code-barres',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Emplacements restaurés',
                    'body'  => 'Les emplacements ont été restaurés avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Emplacements supprimés',
                    'body'  => 'Les emplacements ont été supprimés avec succès.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Emplacements définitivement supprimés',
                        'body'  => 'Les emplacements ont été définitivement supprimés avec succès.',
                    ],

                    'error' => [
                        'title' => 'Les emplacements n\'ont pas pu être supprimés',
                        'body'  => 'Les emplacements ne peuvent pas être supprimés car ils sont actuellement utilisés.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',

                'entries' => [
                    'location'                     => 'Emplacement',
                    'location-placeholder'         => 'ex. Stock de réserve',
                    'parent-location'              => 'Emplacement parent',
                    'parent-location-hint-tooltip' => 'L\'emplacement principal qui englobe cet emplacement. Par exemple, la « Zone d\'expédition » fait partie de l\'emplacement parent « Porte 1 ».',
                    'external-notes'               => 'Notes externes',
                ],
            ],

            'settings' => [
                'title'  => 'Paramètres',

                'entries' => [
                    'location-type'                 => 'Type d\'emplacement',
                    'company'                       => 'Société',
                    'storage-category'              => 'Catégorie de stockage',
                    'is-scrap'                      => 'Est un emplacement de rebut ?',
                    'is-scrap-hint-tooltip'         => 'Cochez cette case pour désigner cet emplacement pour le stockage des marchandises mises au rebut ou endommagées.',
                    'is-dock'                       => 'Est un emplacement de quai ?',
                    'is-dock-hint-tooltip'          => 'Cochez cette case pour désigner cet emplacement pour le stockage des marchandises prêtes à être expédiées.',
                    'is-replenish'                  => 'Est un emplacement de réapprovisionnement ?',
                    'is-replenish-hint-tooltip'     => 'Activez cette fonction pour récupérer toutes les quantités nécessaires au réapprovisionnement de cet emplacement.',
                    'logistics'                     => 'Logistique',
                    'removal-strategy'              => 'Stratégie de retrait',
                    'removal-strategy-hint-tooltip' => 'Spécifie la méthode par défaut pour déterminer l\'étagère, le lot et l\'emplacement exacts à partir desquels prélever les produits. Cette méthode peut être imposée au niveau de la catégorie de produit, avec un repli sur les emplacements parents si elle n\'est pas définie ici.',
                    'cyclic-counting'               => 'Comptage cyclique',
                    'inventory-frequency'           => 'Fréquence d\'inventaire',
                    'last-inventory'                => 'Dernier inventaire',
                    'last-inventory-hint-tooltip'   => 'Date du dernier inventaire à cet emplacement.',
                    'next-expected'                 => 'Prochain prévu',
                    'next-expected-hint-tooltip'    => 'Date du prochain inventaire prévu selon le calendrier cyclique.',
                ],
            ],

            'additional' => [
                'title'  => 'Informations complémentaires',
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
