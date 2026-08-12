<?php

return [
    'navigation' => [
        'title' => 'Ordres de travail',
        'group' => 'Opérations',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Général',
                'fields' => [
                    'work-order'          => 'Ordre de travail',
                    'work-center'         => 'Poste de travail',
                    'product'             => 'Produit',
                    'quantity'            => 'Quantité',
                    'manufacturing-order' => 'Ordre de fabrication',
                    'lot-serial'          => 'Numéro de lot/série',
                    'start-date'          => 'Date de début',
                    'end-date'            => 'Date de fin',
                    'date-range-separator'=> 'à',
                    'expected-duration'   => 'Durée prévue',
                    'duration-suffix'     => 'minutes',
                    'real-duration'       => 'Durée réelle',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'      => 'Suivi du temps',
                'add-action' => 'Ajouter une ligne',
                'columns'    => [
                    'user'         => 'Utilisateur',
                    'duration'     => 'Durée',
                    'start-date'   => 'Date de début',
                    'end-date'     => 'Date de fin',
                    'productivity' => 'Productivité',
                ],
                'footer' => [
                    'real-duration' => 'Durée réelle',
                ],
            ],
            'components' => [
                'title'      => 'Composants',
                'add-action' => 'Ajouter une ligne',
                'columns'    => [
                    'product'    => 'Produit',
                    'from'       => 'De',
                    'to-consume' => 'À consommer',
                    'quantity'   => 'Quantité',
                    'uom'        => 'UDM',
                ],
            ],
            'work-instruction' => [
                'title'   => 'Instruction de travail',
                'entries' => [
                    'operation' => 'Opération',
                    'worksheet' => 'Fiche de travail',
                ],
            ],
            'blocked-by' => [
                'title'  => 'Bloqué par',
                'fields' => [
                    'work-orders' => 'Ordres de travail',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'operation'           => 'Opération',
            'work-center'         => 'Poste de travail',
            'manufacturing-order' => 'Ordre de fabrication',
            'product'             => 'Produit',
            'quantity-remaining'  => 'Quantité restante',
            'lot-serial'          => 'Lot/Série',
            'start'               => 'Début',
            'end'                 => 'Fin',
            'expected-duration'   => 'Durée prévue',
            'real-duration'       => 'Durée réelle',
            'status'              => 'Statut',
        ],
        'groups' => [
            'status'              => 'Statut',
            'work-center'         => 'Poste de travail',
            'manufacturing-order' => 'Ordre de fabrication',
            'product'             => 'Produit',
            'start'               => 'Début',
            'end'                 => 'Fin',
        ],
        'filters' => [
            'work-order'          => 'Ordre de travail',
            'status'              => 'Statut',
            'operation'           => 'Opération',
            'work-center'         => 'Poste de travail',
            'manufacturing-order' => 'Ordre de fabrication',
            'product'             => 'Produit',
            'start'               => 'Début',
            'end'                 => 'Fin',
            'created-at'          => 'Créé le',
            'updated-at'          => 'Mis à jour le',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Général',
                'entries' => [
                    'work-order'          => 'Ordre de travail',
                    'work-center'         => 'Poste de travail',
                    'product'             => 'Produit',
                    'quantity'            => 'Quantité',
                    'manufacturing-order' => 'Ordre de fabrication',
                    'lot-serial'          => 'Numéro de lot/série',
                    'start-date'          => 'Date de début',
                    'end-date'            => 'Date de fin',
                    'expected-duration'   => 'Durée prévue',
                    'real-duration'       => 'Durée réelle',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'  => 'Suivi du temps',
                'footer' => [
                    'real-duration' => 'Durée réelle',
                ],
            ],
            'components' => [
                'title' => 'Composants',
            ],
            'work-instruction' => [
                'title'   => 'Instruction de travail',
                'entries' => [
                    'operation' => 'Opération',
                    'worksheet' => 'Fiche de travail',
                ],
            ],
            'blocked-by' => [
                'title'   => 'Bloqué par',
                'columns' => [
                    'work-order'  => 'Ordre de travail',
                    'work-center' => 'Poste de travail',
                    'status'      => 'Statut',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label' => 'Nouvel ordre de travail',
                ],
            ],
        ],
    ],
];
