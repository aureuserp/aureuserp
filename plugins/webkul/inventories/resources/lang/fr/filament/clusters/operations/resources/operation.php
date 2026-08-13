<?php

return [
    'navigation' => [
        'title' => 'Produits',
        'group' => 'Inventaire',
    ],

    'global-search' => [
        'partner' => 'Partenaire',
        'origin'  => 'Origine',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Général',

                'fields' => [
                    'receive-from'         => 'Recevoir de',
                    'contact'              => 'Contact',
                    'delivery-address'     => 'Adresse de livraison',
                    'operation-type'       => 'Type d\'opération',
                    'source-location'      => 'Emplacement source',
                    'destination-location' => 'Emplacement de destination',
                ],
            ],

            'additional-fields' => [
                'title' => 'Informations complémentaires',
            ],
        ],

        'tabs' => [
            'operations' => [
                'title' => 'Opérations',

                'columns' => [
                    'product'                    => 'Produit',
                    'final-location'             => 'Emplacement final',
                    'description'                => 'Description',
                    'scheduled-at'               => 'Planifié le',
                    'deadline'                   => 'Échéance',
                    'packaging'                  => 'Conditionnement',
                    'demand'                     => 'Demande',
                    'quantity'                   => 'Quantité',
                    'insufficient-stock-tooltip' => 'Quantité disponible insuffisante',
                    'unit'                       => 'Unité',
                    'picked'                     => 'Prélevé',
                ],

                'actions' => [
                    'open-product' => [
                        'tooltip' => 'Ouvrir le produit',
                    ],
                ],

                'fields' => [
                    'product'        => 'Produit',
                    'final-location' => 'Emplacement final',
                    'description'    => 'Description',
                    'scheduled-at'   => 'Planifié le',
                    'deadline'       => 'Échéance',
                    'packaging'      => 'Conditionnement',
                    'demand'         => 'Demande',
                    'quantity'       => 'Quantité',
                    'unit'           => 'Unité',
                    'picked'         => 'Prélevé',

                    'lines' => [
                        'modal-heading'             => 'Gérer les mouvements de stock',
                        'modal-submit-action-label' => 'Enregistrer',
                        'add-line'                  => 'Ajouter une ligne',

                        'actions' => [
                            'generate' => 'Générer les numéros de série/lots',
                            'import'   => 'Importer les numéros de série/lots',
                        ],

                        'fields' => [
                            'lot'                => 'Numéro de lot/série',
                            'pick-from'          => 'Prélever depuis',
                            'location'           => 'Stocker vers',
                            'package'            => 'Colis de destination',
                            'quantity'           => 'Quantité',
                            'uom'                => 'Unité de mesure',
                            'first-lot'          => 'Premier numéro de lot',
                            'quantity-per-lot'   => 'Quantité par lot',
                            'quantity-received'  => 'Quantité reçue',
                            'keep-current-lines' => 'Conserver les lignes actuelles',
                            'serials'            => 'Numéros de lot/série',
                            'serials-helper'     => 'Un numéro de lot/série par ligne.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Complémentaire',

                'fields' => [
                    'responsible'                  => 'Responsable',
                    'shipping-policy'              => 'Politique d\'expédition',
                    'shipping-policy-hint-tooltip' => 'Elle définit si les marchandises doivent être livrées partiellement ou en une seule fois.',
                    'scheduled-at'                 => 'Planifié le',
                    'scheduled-at-hint-tooltip'    => 'L\'heure planifiée pour le traitement de la première partie de l\'expédition. La définition manuelle d\'une valeur ici l\'appliquera comme date prévue pour tous les mouvements de stock.',
                    'source-document'              => 'Document source',
                    'source-document-hint-tooltip' => 'Référence du document',
                ],
            ],

            'note' => [
                'title' => 'Note',

                'fields' => [

                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'        => 'Favori',
            'reference'       => 'Référence',
            'from'            => 'De',
            'to'              => 'À',
            'contact'         => 'Contact',
            'responsible'     => 'Responsable',
            'scheduled-at'    => 'Planifié le',
            'deadline'        => 'Échéance',
            'closed-at'       => 'Clôturé le',
            'source-document' => 'Document source',
            'operation-type'  => 'Type d\'opération',
            'company'         => 'Société',
            'state'           => 'État',
            'deleted-at'      => 'Supprimé le',
            'created-at'      => 'Créé le',
            'updated-at'      => 'Mis à jour le',
        ],

        'groups' => [
            'state'           => 'État',
            'source-document' => 'Document source',
            'operation-type'  => 'Type d\'opération',
            'scheduled-at'    => 'Planifié le',
            'created-at'      => 'Créé le',
        ],

        'filters' => [
            'operation-type'       => 'Type d\'opération',
            'name'                 => 'Nom',
            'state'                => 'État',
            'partner'              => 'Partenaire',
            'responsible'          => 'Responsable',
            'owner'                => 'Propriétaire',
            'source-location'      => 'Emplacement source',
            'destination-location' => 'Emplacement de destination',
            'deadline'             => 'Échéance',
            'scheduled-at'         => 'Planifié le',
            'closed-at'            => 'Clôturé le',
            'created-at'           => 'Créé le',
            'updated-at'           => 'Mis à jour le',
            'company'              => 'Société',
            'creator'              => 'Créateur',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Informations générales',
                'entries' => [
                    'contact'              => 'Contact',
                    'operation-type'       => 'Type d\'opération',
                    'source-location'      => 'Emplacement source',
                    'destination-location' => 'Emplacement de destination',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title'   => 'Opérations',
                'entries' => [
                    'product'        => 'Produit',
                    'final-location' => 'Emplacement final',
                    'description'    => 'Description',
                    'scheduled-at'   => 'Planifié le',
                    'deadline'       => 'Échéance',
                    'packaging'      => 'Conditionnement',
                    'demand'         => 'Demande',
                    'quantity'       => 'Quantité',
                    'unit'           => 'Unité',
                    'picked'         => 'Prélevé',
                ],
            ],
            'additional' => [
                'title'   => 'Informations complémentaires',
                'entries' => [
                    'responsible'     => 'Responsable',
                    'shipping-policy' => 'Politique d\'expédition',
                    'scheduled-at'    => 'Planifié le',
                    'source-document' => 'Document source',
                ],
            ],
            'note' => [
                'title' => 'Note',
            ],
        ],
    ],

    'tabs' => [
        'todo'        => 'À faire',
        'my'          => 'Mes transferts',
        'starred'     => 'Favoris',
        'draft'       => 'Brouillon',
        'waiting'     => 'En attente',
        'ready'       => 'Prêt',
        'done'        => 'Terminé',
        'canceled'    => 'Annulé',
        'back-orders' => 'Reliquats',
    ],

    'notifications' => [
        'uom-precision-warning' => [
            'title' => 'Avertissement de précision de l\'unité de mesure',
            'body'  => 'Vous utilisez une unité de mesure plus petite que celle utilisée pour stocker ce produit. Cela peut entraîner des problèmes d\'arrondi sur les quantités réservées. Pensez à utiliser la plus petite unité de mesure pour la valorisation du stock, ou réduisez la précision d\'arrondi de votre unité de base.',
        ],
    ],
];
