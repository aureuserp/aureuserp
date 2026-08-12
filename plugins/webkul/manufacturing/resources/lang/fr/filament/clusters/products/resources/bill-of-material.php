<?php

return [
    'navigation' => [
        'title' => 'Nomenclatures',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',
                'fields' => [
                    'reference'             => 'Référence',
                    'reference-placeholder' => 'ex. BOM-001',
                    'product'               => 'Produit',
                    'product-variant'       => 'Variante de produit',
                    'quantity'              => 'Quantité',
                    'uom'                   => 'UDM',
                    'operation-type'        => 'Type d\'opération',
                    'company'               => 'Société',
                    'type'                  => 'Type de nomenclature',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Divers',
                'fields' => [
                    'kit-information'                         => 'Informations sur le kit',
                    'kit-information-content'                 => 'Une nomenclature de type kit est utilisée pour regrouper des composants en vue de transferts ou de ventes, au lieu d\'être produite via un ordre de fabrication.',
                    'manufacturing-lead-time'                 => 'Délai de fabrication',
                    'days-to-prepare-manufacturing-order'     => 'Jours pour préparer l\'ordre de fabrication',
                    'days-suffix'                             => 'jours',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'      => 'Composants',
                'add-action' => 'Ajouter une ligne',
                'columns'    => [
                    'component'              => 'Composant',
                    'apply-on-variants'      => 'Appliquer sur les variantes',
                    'consumed-in-operation'  => 'Consommé dans l\'opération',
                    'highlight-consumption'  => 'Mettre en évidence la consommation',
                    'quantity'               => 'Quantité',
                    'uom'                    => 'Unité de mesure du produit',
                ],
                'validation' => [
                    'component-different-from-product' => 'Le composant doit être différent du produit fabriqué.',
                ],
                'create-form' => [
                    'fields' => [
                        'name'            => 'Nom',
                        'type'            => 'Type',
                        'category'        => 'Catégorie',
                        'company'         => 'Société',
                        'uom'             => 'UDM',
                        'uom-placeholder' => 'UDM',
                    ],
                ],
            ],
            'operations' => [
                'title'      => 'Opérations',
                'add-action' => 'Ajouter une ligne',
                'actions'    => [
                    'edit'                 => 'Modifier l\'opération',
                    'copy-existing'        => 'Copier des opérations existantes',
                    'copy-existing-fields' => [
                        'operation' => 'Opération',
                    ],
                ],
                'columns'    => [
                    'operation'        => 'Opération',
                    'work-center'      => 'Poste de travail',
                    'time-mode'        => 'Calcul de la durée',
                    'time-mode-batch'  => 'Calculé sur les derniers',
                    'company'          => 'Société',
                    'apply-on-variants'=> 'Appliquer sur les variantes',
                    'duration'         => 'Durée (minutes)',
                ],
            ],
            'by-products' => [
                'title'      => 'Sous-produits',
                'add-action' => 'Ajouter une ligne',
                'columns'    => [
                    'product'   => 'Sous-produit',
                    'quantity'  => 'Quantité',
                    'uom'       => 'Unité de mesure',
                    'operation' => 'Produit dans l\'opération',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Divers',
                'fields' => [
                    'ready-to-produce'       => 'Disponibilité de fabrication',
                    'routing'                => 'Gamme opératoire',
                    'consumption'            => 'Consommation flexible',
                    'operation-dependencies' => 'Dépendances des opérations',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'  => 'Référence',
            'product'    => 'Produit',
            'quantity'   => 'Quantité',
            'uom'        => 'UDM',
            'type'       => 'Type de nomenclature',
            'company'    => 'Société',
            'deleted-at' => 'Supprimé le',
            'updated-at' => 'Mis à jour le',
        ],
        'filters' => [
            'product' => 'Produit',
            'type'    => 'Type de nomenclature',
            'company' => 'Société',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Nomenclature restaurée',
                    'body'  => 'La nomenclature a été restaurée avec succès.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Nomenclature archivée',
                    'body'  => 'La nomenclature a été archivée avec succès.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Nomenclature supprimée',
                        'body'  => 'La nomenclature a été définitivement supprimée.',
                    ],
                    'error' => [
                        'title' => 'Impossible de supprimer la nomenclature',
                        'body'  => 'La nomenclature ne peut pas être supprimée car elle est actuellement utilisée.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Nomenclatures restaurées',
                    'body'  => 'Les nomenclatures sélectionnées ont été restaurées avec succès.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Nomenclatures archivées',
                    'body'  => 'Les nomenclatures sélectionnées ont été archivées avec succès.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Nomenclatures supprimées',
                        'body'  => 'Les nomenclatures sélectionnées ont été définitivement supprimées.',
                    ],
                    'error' => [
                        'title' => 'Impossible de supprimer les nomenclatures',
                        'body'  => 'Une ou plusieurs nomenclatures sélectionnées sont actuellement utilisées.',
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
                    'reference'      => 'Référence',
                    'product'        => 'Produit',
                    'product-variant'=> 'Variante de produit',
                    'quantity'       => 'Quantité',
                    'uom'            => 'UDM',
                    'operation-type' => 'Type d\'opération',
                    'company'        => 'Société',
                    'type'           => 'Type de nomenclature',
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
        'tabs' => [
            'components' => [
                'title'   => 'Composants',
                'entries' => [
                    'component' => 'Composant',
                    'operation' => 'Opération',
                    'quantity'  => 'Quantité',
                    'uom'       => 'Unité de mesure du produit',
                ],
            ],
            'operations' => [
                'title'   => 'Opérations',
                'entries' => [
                    'operation'   => 'Opération',
                    'work-center' => 'Poste de travail',
                    'time-mode'   => 'Calcul de la durée',
                    'duration'    => 'Durée (minutes)',
                ],
            ],
            'by-products' => [
                'title'   => 'Sous-produits',
                'entries' => [
                    'product'   => 'Sous-produit',
                    'quantity'  => 'Quantité',
                    'uom'       => 'Unité de mesure',
                    'operation' => 'Produit dans l\'opération',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Divers',
                'entries' => [
                    'kit-information'                         => 'Informations sur le kit',
                    'kit-information-content'                 => 'Une nomenclature de type kit est utilisée pour regrouper des composants en vue de transferts ou de ventes, au lieu d\'être produite via un ordre de fabrication.',
                    'ready-to-produce'                        => 'Disponibilité de fabrication',
                    'routing'                                 => 'Gamme opératoire',
                    'consumption'                             => 'Consommation flexible',
                    'operation-dependencies'                  => 'Dépendances des opérations',
                    'manufacturing-lead-time'                 => 'Délai de fabrication',
                    'days-to-prepare-manufacturing-order'     => 'Jours pour préparer l\'ordre de fabrication',
                    'days-suffix'                             => 'jours',
                ],
            ],
        ],
    ],
];
