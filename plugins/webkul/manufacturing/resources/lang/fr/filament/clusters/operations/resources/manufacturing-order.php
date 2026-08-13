<?php

return [
    'navigation' => [
        'title' => 'Ordres de fabrication',
        'group' => 'Opérations',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',
                'fields' => [
                    'product'                => 'Produit',
                    'quantity'               => 'Quantité',
                    'uom'                    => 'UDM',
                    'bill-of-material'       => 'Nomenclature',
                    'scheduled-date'         => 'Date planifiée',
                    'scheduled-end'          => 'Fin planifiée',
                    'responsible'            => 'Responsable',
                    'to-produce'             => 'À produire',
                    'to-produce-placeholder' => 'Aperçu de l\'image',
                    'uom-placeholder'        => 'UDM',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Composants',
                'add-action'   => 'Ajouter une ligne',
                'process-note' => 'Les composants seront générés au fur et à mesure de la construction du processus de fabrication.',
                'columns'      => [
                    'component'          => 'Produit',
                    'from'               => 'De',
                    'to-consume'         => 'À consommer',
                    'to-consume-tooltip' => 'Quantité disponible insuffisante',
                    'quantity'           => 'Quantité',
                    'uom'                => 'UDM',
                    'forecast'           => 'Prévision',
                ],
            ],
            'work-orders' => [
                'title'        => 'Ordres de travail',
                'add-action'   => 'Ajouter une ligne',
                'process-note' => 'Les ordres de travail seront générés une fois le processus de fabrication configuré.',
                'columns'      => [
                    'operation'          => 'Opération',
                    'work-center'        => 'Poste de travail',
                    'product'            => 'Produit',
                    'quantity-remaining' => 'Quantité restante',
                    'quantity-produced'  => 'Quantité produite',
                    'start'              => 'Début',
                    'end'                => 'Fin',
                    'expected-duration'  => 'Durée prévue',
                    'real-duration'      => 'Durée réelle',
                    'status'             => 'Statut',
                    'lot-serial'         => 'Lot/Série',
                ],
                'actions'      => [
                    'open-work-order' => [
                        'tooltip' => 'Ouvrir l\'ordre de travail',
                    ],

                    'done' => [
                        'label' => 'Terminé',
                    ],
                ],
            ],
            'by-products' => [
                'title'        => 'Sous-produits',
                'process-note' => 'Les sous-produits seront générés au fur et à mesure de la construction du processus de fabrication.',
                'columns'      => [
                    'product'    => 'Produit',
                    'to'         => 'Vers',
                    'to-produce' => 'À produire',
                    'uom'        => 'UDM',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Divers',
                'fields' => [
                    'operation-type'             => 'Type d\'opération',
                    'source'                     => 'Source',
                    'finished-products-location' => 'Emplacement des produits finis',
                    'company'                    => 'Société',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'              => 'Référence',
            'start'                  => 'Début',
            'end'                    => 'Fin',
            'deadline'               => 'Échéance',
            'product'                => 'Produit',
            'lot-serial-number'      => 'Numéro de lot/série',
            'bill-of-material'       => 'Nomenclature',
            'source'                 => 'Source',
            'responsible'            => 'Responsable',
            'mo-readiness'           => 'Disponibilité de l\'OF',
            'component-status'       => 'Statut des composants',
            'quantity'               => 'Quantité',
            'uom'                    => 'UDM',
            'consumption-efficiency' => 'Efficacité de consommation',
            'expected-duration'      => 'Durée prévue',
            'real-duration'          => 'Durée réelle',
            'company'                => 'Société',
            'state'                  => 'État',
        ],
        'groups' => [
            'state'            => 'État',
            'product'          => 'Produit',
            'bill-of-material' => 'Nomenclature',
            'responsible'      => 'Responsable',
            'deadline'         => 'Échéance',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Général',
                'entries' => [
                    'product'                  => 'Produit',
                    'scheduled-date'           => 'Date planifiée',
                    'responsible'              => 'Responsable',
                    'quantity'                 => 'Quantité',
                    'uom'                      => 'UDM',
                    'bill-of-material'         => 'Nomenclature',
                    'operation-type'           => 'Type d\'opération',
                    'consumption-efficiency'   => 'Efficacité de consommation',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Composants',
                'process-note' => 'Les composants seront disponibles une fois le processus de fabrication configuré.',
                'columns'      => [
                    'component' => 'Composant',
                    'quantity'  => 'Quantité',
                    'uom'       => 'UDM',
                ],
            ],
            'work-orders' => [
                'title'        => 'Ordres de travail',
                'process-note' => 'Les ordres de travail seront disponibles une fois le processus de fabrication configuré.',
                'columns'      => [
                    'operation'          => 'Opération',
                    'work-center'        => 'Poste de travail',
                    'product'            => 'Produit',
                    'quantity-remaining' => 'Quantité restante',
                    'expected-duration'  => 'Durée prévue',
                    'real-duration'      => 'Durée réelle',
                    'lot-serial'         => 'Lot/Série',
                    'start'              => 'Début',
                    'end'                => 'Fin',
                ],
            ],
            'by-products' => [
                'title'        => 'Sous-produits',
                'process-note' => 'Les sous-produits seront disponibles une fois le processus de fabrication configuré.',
                'columns'      => [
                    'product'    => 'Produit',
                    'to'         => 'Vers',
                    'to-produce' => 'À produire',
                    'uom'        => 'UDM',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Divers',
                'entries' => [
                    'operation-type'             => 'Type d\'opération',
                    'source'                     => 'Source',
                    'finished-products-location' => 'Emplacement des produits finis',
                    'company'                    => 'Société',
                ],
            ],
        ],
    ],

    'pages' => [
        'shared' => [
            'header-actions' => [
                'confirm' => [
                    'label'        => 'Confirmer',
                    'notification' => [
                        'title' => 'Ordre de fabrication confirmé',
                    ],
                ],

                'cancel' => [
                    'label'        => 'Annuler',
                    'notification' => [
                        'title' => 'Ordre de fabrication annulé',
                    ],
                ],
            ],
        ],
    ],
];
